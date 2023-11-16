# Better Website Performance

The Better Website Performance plugin adds advanced features to improve website performance.

Main features include HTML Head optimization, javascript async, CSS concat and inline, resource hints, etc.

## Installation

1. Download and unzip files. Or install Better Website Performance plugin using the WordPress plugin installer. In that case, skip 2.
2. Upload "better-website-performance" to the "/wp-content/plugins/" directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Access Customizer Panel `Performance Settings (Better Website Performance)`.
5. Have fun!

## Descriptions of features

### HTML Head optimization

Manage meta tags and rel=link output by WordPress

### Emoji resource

Manage WordPress Emoji features and resources

### image srcset

Manage image srcset (Responsive image)

### JavaScript async

Asynchronous JavaScript managed by WordPress

### jQuery

Manage jQuery loading

### CSS concat and inline

Concating, inlining or minify stylesheets

### Custom CSS

Place Custom CSS output by WordPress in the footer.

### Resource Hints

Manage resource prefetching

### Preload

Manage resource prefetching

## WordPress Plugin Directory

Better Website Performance is hosted on the WordPress Plugin Directory.

[https://wordpress.org/plugins/better-website-performance/](https://wordpress.org/plugins/better-website-performance/)

## Support

If you have any trouble, you can use the forums or report bugs.

* Forum: [https://wordpress.org/support/plugin/better-website-performance/](https://wordpress.org/support/plugin/better-website-performance/)
* Issues: [https://github.com/thingsym/better-website-performance/issues](https://github.com/thingsym/better-website-performance/issues)

## Contribution

Small patches and bug reports can be submitted a issue tracker in Github.

Translating a plugin takes a lot of time, effort, and patience. I really appreciate the hard work from these contributors.

If you have created or updated your own language pack, you can send gettext PO and MO files to author. I can bundle it into plugin.

* VCS - Github: [https://github.com/thingsym/better-website-performance/](https://github.com/thingsym/better-website-performance/)
* [Translate Better Website Performance into your language.](https://translate.wordpress.org/projects/wp-plugins/better-website-performance)

You can also contribute by answering issues on the forums.

* Forum: [https://wordpress.org/support/plugin/better-website-performance/](https://wordpress.org/support/plugin/better-website-performance/)
* Issues: [https://github.com/thingsym/better-website-performance/issues](https://github.com/thingsym/better-website-performance/issues)

### Patches and Bug Fixes

Forking on Github is another good way. You can send a pull request.

1. Fork [Better Website Performance](https://github.com/thingsym/better-website-performance) from GitHub repository
2. Create a feature branch: git checkout -b my-new-feature
3. Commit your changes: git commit -am 'Add some feature'
4. Push to the branch: git push origin my-new-feature
5. Create new Pull Request

### Contribute guidlines

If you would like to contribute, here are some notes and guidlines.

* All development happens on the **main** branch, so it is always the most up-to-date
* If you are going to be submitting a pull request, please submit your pull request to the **main** branch
* See about [forking](https://help.github.com/articles/fork-a-repo/) and [pull requests](https://help.github.com/articles/using-pull-requests/)

## Test Matrix

For operation compatibility between PHP version and WordPress version, see below [Github Actions](https://github.com/thingsym/better-website-performance/actions).

## Changelog

### [1.1.1] - 2023.11.16

* update japanese translation
* update pot
* tested up to 6.4.1
* fix workflows
* phpunit-polyfills bump up
* improve sanitize_key function for dot
* remove @charset "UTF-8"; into css

### [1.1.0] - 2023.06.09

* add test case
* add uninstall process
* add public class property
* fix test case
* ci support php version 8.0 later
* add github actions for deploy to wordpress.org

### [1.0.1] - 2023.05.31

* imporve code with phpcs
* change plugin name to Better Website Performance

### [1.0.0] - 2023.05.23

* initial release

## License

Licensed under [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).
