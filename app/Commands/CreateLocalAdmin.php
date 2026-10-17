<?php

namespace App\Commands;

use App\Services\LocalAdminBootstrapService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class CreateLocalAdmin extends BaseCommand
{
    protected $group = 'Duno';
    protected $name = 'admin:create-local';
    protected $description = 'Create the one-time local Duno admin account.';
    protected $usage = 'admin:create-local';
    protected $arguments = [];
    protected $options = [];

    public function run(array $params)
    {
        $database = config(Database::class)->default;
        if (! LocalAdminBootstrapService::isAllowedEnvironment(ENVIRONMENT, $database['hostname'] ?? '')) {
            CLI::error('This setup command requires development mode and a loopback-only database connection.');

            return EXIT_ERROR;
        }

        $bootstrap = new LocalAdminBootstrapService();
        if ($bootstrap->adminExists()) {
            CLI::error('An admin account already exists. No account was created.');

            return EXIT_ERROR;
        }

        $username = CLI::prompt('Admin username', null, 'required|alpha_numeric_punct|min_length[3]|max_length[30]');
        $email = CLI::prompt('Admin email', null, 'required|valid_email|max_length[254]');
        $account = $bootstrap->create($username, $email);
        if ($account === null) {
            CLI::error('An admin account already exists. No account was created.');

            return EXIT_ERROR;
        }

        CLI::write('Local admin account created.');
        CLI::write('Generated password (shown once; copy it now): ' . $account['password'], 'yellow');
        CLI::write('The password is not saved in a file or application log.');

        return EXIT_SUCCESS;
    }
}
