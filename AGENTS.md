# Krumo — agent guidance

## Entry points
- `class.krumo.php` — single-file library (1700 lines, no namespace, no PSR-4)
- Autoloaded via `"files": ["class.krumo.php"]` in `composer.json`
- Global functions `krumo()`, `k()`, `kd()` in the same file (line ~1655)
- OOP entry: `Krumo::dump($data)` (line 441)

## Tests
- **No test runner.** `tests/php/` contains ad-hoc PHP scripts (visual inspection only, no assertions, no PHPUnit)
- Run in browser or CLI: `php tests/php/misc.php`

## Config
- `krumo.ini` is gitignored; `krumo.sample.ini` is the template
- Skin selected via `[skin] selected = "name"` in ini; default = `stylish`

## Skins
- 7 skins under `skins/` — CSS is injected inline (not linked), `%url%` placeholder replaced at runtime
- IE6 `* html` hacks exist in some skin CSS files — remove if modernizing

## JS
- `js/krumo.js` — vanilla JS, no dependencies. Minified via: `curl -X POST -s --data-urlencode 'input@js/krumo.js' https://www.toptal.com/developers/javascript-minifier/api/raw > js/krumo.min.js`

## PHP requirements
- `"php": ">=8.2"`

## Gotchas
- `dump()` uses `func_get_args()` + `call_user_func_array()` — don't add variadic without updating all callers
- Array recursion detection stains the array by injecting a marker key (`&$data` by-reference). This is intentional and harmless (marker is filtered from display). Don't remove `&` signatures without updating the staining logic.

- CLI mode falls back to `var_export()`, not the HTML renderer
