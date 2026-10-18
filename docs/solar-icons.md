# Solar Icons

Duno uses a small, local subset of Solar Icons as static SVG assets. This keeps the app compatible with server-rendered CodeIgniter views and FTP deployment; there is no runtime icon package or external CDN dependency.

## Installed subset

The SVG files are copied unchanged from `@solar-icons/static` version `2.3.3`, Linear style, into `public/assets/icons/solar/`.

| File | Interface use |
| --- | --- |
| `add-linear.svg` | Add a subject |
| `book-2-linear.svg` | Duno Guide |
| `calendar-linear.svg` | Duno Plan navigation |
| `chart-2-linear.svg` | Aralytics and admin dashboard navigation |
| `filter-linear.svg` | Topic filters |
| `hamburger-menu-linear.svg` | Compact header menus |
| `home-linear.svg` | Home navigation |
| `magnifier-linear.svg` | Subject search |
| `stopwatch-linear.svg` | Duno Timer navigation |
| `target-linear.svg` | Duno Focus navigation |

`public/assets/css/student-navigation.css` exposes these files as colorable CSS masks using the `.solar-icon` utility classes. Keep icon alternatives decorative when nearby text already names the action. Add only icons used by a real app control or section.

## Attribution and license

Solar Icons is maintained by Hakim Saoudi. The icon artwork is the Solar Icon Set by 480 Design and is licensed under [Creative Commons Attribution 4.0 International](https://creativecommons.org/licenses/by/4.0/). Duno includes the upstream `LICENSE` and `LICENSE-THIRD-PARTY` files beside the assets. The app footer also provides visible attribution. The Solar Icons package version and license files are retained here for provenance; no npm runtime dependency is installed.

Source: [Solar Icons catalog](https://solar-icons.vercel.app/), [Solar Icons source repository](https://github.com/saoudi-h/solar-icons).
