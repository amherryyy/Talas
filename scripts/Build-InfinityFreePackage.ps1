[CmdletBinding()]
param(
    [string] $OutputDirectory
)

$ErrorActionPreference = 'Stop'

$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$buildRoot = Join-Path $projectRoot 'build'
$php = 'C:\xampp\php\php.exe'
$composer = Join-Path $projectRoot '.tools\composer.phar'
$utf8NoBom = [System.Text.UTF8Encoding]::new($false)

if (-not (Test-Path -LiteralPath $php -PathType Leaf)) {
    throw "XAMPP PHP was not found at '$php'. Update the PHP path in this script for this workstation."
}

if (-not (Test-Path -LiteralPath $composer -PathType Leaf)) {
    throw "The project-local Composer PHAR was not found at '$composer'. Follow docs/deployment.md to prepare Composer first."
}

if (-not (Test-Path -LiteralPath (Join-Path $projectRoot 'vendor') -PathType Container)) {
    throw 'The local vendor directory is missing. Install locked Composer dependencies before preparing the hosting package.'
}

if ([string]::IsNullOrWhiteSpace($OutputDirectory)) {
    $stamp = Get-Date -Format 'yyyyMMdd-HHmmss'
    $OutputDirectory = Join-Path $buildRoot "infinityfree-htdocs-$stamp"
}

$packageRoot = [System.IO.Path]::GetFullPath($OutputDirectory)
$resolvedBuildRoot = [System.IO.Path]::GetFullPath($buildRoot).TrimEnd([System.IO.Path]::DirectorySeparatorChar) + [System.IO.Path]::DirectorySeparatorChar
if (-not $packageRoot.StartsWith($resolvedBuildRoot, [System.StringComparison]::OrdinalIgnoreCase)) {
    throw "Output must be a new directory inside '$buildRoot'. Received '$packageRoot'."
}

if (Test-Path -LiteralPath $packageRoot) {
    throw "Output already exists; choose a new path so existing files are never overwritten: '$packageRoot'."
}

if (-not (Test-Path -LiteralPath $buildRoot -PathType Container)) {
    New-Item -ItemType Directory -Path $buildRoot | Out-Null
}

New-Item -ItemType Directory -Path $packageRoot | Out-Null

try {
    Copy-Item -LiteralPath (Join-Path $projectRoot 'app') -Destination (Join-Path $packageRoot 'app') -Recurse

    $assets = Join-Path $projectRoot 'public\assets'
    if (-not (Test-Path -LiteralPath $assets -PathType Container)) {
        throw "Public assets directory is missing: '$assets'."
    }
    Copy-Item -LiteralPath $assets -Destination (Join-Path $packageRoot 'assets') -Recurse

    foreach ($publicFile in @('favicon.ico', 'robots.txt')) {
        $sourceFile = Join-Path $projectRoot "public\$publicFile"
        if (Test-Path -LiteralPath $sourceFile -PathType Leaf) {
            Copy-Item -LiteralPath $sourceFile -Destination (Join-Path $packageRoot $publicFile)
        }
    }

    $entryPoint = Get-Content -LiteralPath (Join-Path $projectRoot 'public\index.php') -Raw
    $oldPathsInclude = "require FCPATH . '../app/Config/Paths.php';"
    $rootPathsInclude = "require FCPATH . 'app/Config/Paths.php';"
    if (-not $entryPoint.Contains($oldPathsInclude)) {
        throw 'The public front controller no longer has the expected Paths.php include; review the packaging change before continuing.'
    }
    $entryPoint = $entryPoint.Replace($oldPathsInclude, $rootPathsInclude)
    $localMarkerInclude = "require_once __DIR__ . '/../app/Services/InfinityFreeRouteMarker.php';"
    $rootMarkerInclude = "require_once __DIR__ . '/app/Services/InfinityFreeRouteMarker.php';"
    if (-not $entryPoint.Contains($localMarkerInclude)) {
        throw 'The public front controller no longer has the expected InfinityFree marker include; review the packaging change before continuing.'
    }
    $entryPoint = $entryPoint.Replace($localMarkerInclude, $rootMarkerInclude)
    [System.IO.File]::WriteAllText((Join-Path $packageRoot 'index.php'), $entryPoint, $utf8NoBom)

    $rootHtaccess = @'
Options -Indexes
Options +FollowSymLinks
ServerSignature Off

<IfModule mod_rewrite.c>
    RewriteEngine On

    # Deny application source, dependencies, runtime data, development tools, and hidden files.
    RewriteRule ^(?:app|build|builds|docs|node_modules|public|system|tests|vendor|writable)(?:/|$) - [F,L,NC]
    RewriteRule ^(?:composer(?:\.(?:json|lock))?|env(?:\..*)?|spark|preload\.php|phpunit(?:\..*)?|AGENTS\.md|README\.md|LICENSE(?:\..*)?)(?:/|$) - [F,L,NC]
    RewriteRule (^|/)\. - [F,L]
    RewriteRule ^(?:error_log|php_errorlog|php_error\.log)$ - [F,L,NC]
    RewriteRule \.(?:bak|backup|db|dist|dump|gz|ini|log|old|orig|phar|save|sql|sqlite|swp|tar|tgz|tmp|zip)$ - [F,L,NC]

    # Pass authorization headers through to CodeIgniter and route non-files to its front controller.
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?/$1 [L,NC,QSA]
</IfModule>

<IfModule !mod_rewrite.c>
    ErrorDocument 404 index.php
</IfModule>

# Cache file-versioned static assets only. App pages include private student data.
<IfModule mod_expires.c>
    ExpiresActive On
    <FilesMatch "\.(?:css|js|png|gif|jpe?g|svg|webp|ico|woff2?)$">
        ExpiresDefault "access plus 7 days"
    </FilesMatch>
</IfModule>

<IfModule mod_headers.c>
    <FilesMatch "\.(?:css|js|png|gif|jpe?g|svg|webp|ico|woff2?)$">
        Header set Cache-Control "public, max-age=604800"
    </FilesMatch>
</IfModule>
'@
    [System.IO.File]::WriteAllText((Join-Path $packageRoot '.htaccess'), $rootHtaccess, $utf8NoBom)

    $writableSource = Join-Path $projectRoot 'writable'
    $writableTarget = Join-Path $packageRoot 'writable'
    New-Item -ItemType Directory -Path $writableTarget | Out-Null
    Copy-Item -LiteralPath (Join-Path $writableSource '.htaccess') -Destination (Join-Path $writableTarget '.htaccess')
    Copy-Item -LiteralPath (Join-Path $writableSource 'index.html') -Destination (Join-Path $writableTarget 'index.html')
    foreach ($directory in @('cache', 'debugbar', 'logs', 'session', 'uploads')) {
        $targetDirectory = Join-Path $writableTarget $directory
        New-Item -ItemType Directory -Path $targetDirectory | Out-Null
        Set-Content -LiteralPath (Join-Path $targetDirectory 'index.html') -Value '<!doctype html><title>Forbidden</title>' -Encoding utf8
    }

    Copy-Item -LiteralPath (Join-Path $projectRoot 'composer.json') -Destination $packageRoot
    Copy-Item -LiteralPath (Join-Path $projectRoot 'composer.lock') -Destination $packageRoot
    Copy-Item -LiteralPath (Join-Path $projectRoot 'vendor') -Destination (Join-Path $packageRoot 'vendor') -Recurse

    Push-Location $packageRoot
    try {
        & $php -d extension=php_intl.dll -d extension=php_zip.dll $composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
        if ($LASTEXITCODE -ne 0) {
            throw "Composer production dependency install failed with exit code $LASTEXITCODE."
        }
    }
    finally {
        Pop-Location
    }

    Remove-Item -LiteralPath (Join-Path $packageRoot 'composer.json')
    Remove-Item -LiteralPath (Join-Path $packageRoot 'composer.lock')

    if (Test-Path -LiteralPath (Join-Path $packageRoot '.env')) {
        throw 'A .env file was unexpectedly included in the package.'
    }

    Write-Output "InfinityFree package assembled at: $packageRoot"
    Write-Output 'The package has no .env file. Add host-specific settings only after the package is reviewed and uploaded.'
}
catch {
    Write-Error "Package assembly stopped. Partial output was left at '$packageRoot' for inspection and was not uploaded. $($_.Exception.Message)"
    throw
}
