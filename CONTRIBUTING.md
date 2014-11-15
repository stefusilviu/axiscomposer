# How to contribute

Community made patches, localisations, bug reports and contributions are always welcome and are crucial to ensure Axis Builder remains the #1 builder for WordPress that helps you build modern and unique page layouts smartly. This should be as easy as possible for you but there are few things to consider when contributing. The following guidelines for contribution should be followed if you want to submit a pull request.

__Note:__

GitHub is for *bug reports and contributions only* - if you have a support question or a request for a customization don't post here. Use [AxisThemes Support](http://support.axisthemes.com) for customer support, [WordPress.org](http://wordpress.org/support/plugin/axis-builder) for community support, and for customisations we recommend one of the following services:

- [Codeable](https://codeable.io/)
- [Elto](https://www.elto.com/)
- [Affiliated Axis Workers](http://axisthemes.com/affiliated-axis-workers/)

## How to prepare

* Make sure you have a [GitHub account](https://github.com/signup/free)
* Submit an [issue ticket](https://github.com/axisthemes/axis-builder/issues) for your issue, assuming one does not already exist.
	* Clearly Describe the issue including steps to reproduce when it is a bug.
	* Ensure to mention the earliest version that you know is affected.
* Fork the repository on GitHub

## Make Changes

* In your forked repository, create a topic branch for your upcoming patch.
	* Usually this is based on the master branch.
	* Create a branch based on master; `git branch
	fix/master/my_contribution master` then checkout the new branch with `git
	checkout fix/master/my_contribution`. Please avoid working directly on the `master` branch.
* Make the changes to a topic branch in your fork of the repository.
  * **Ensure you stick to the [WordPress Coding Standards](http://make.wordpress.org/core/handbook/coding-standards/php/).**
  * Ensure you use LF line endings - no crazy windows line endings. :)
* Check for unnecessary whitespace with `git diff --check` before committing.
* When committing, reference your issue (#1234) and include a note about the fix.

## Submit Changes

* Push your changes to a topic branch in your fork of the repository.
* Open a pull request to the master branch of the Axis Builder repository and choose the right original branch you want to patch. Existing maintenance branches will be maintained of by Axis Builder developers.
* If not done in commit messages (which you really should do) please reference and update your issue with the code changes.
* Please don't modify the changelog, this will be maintained by Axis Builder developers.

At this point you're waiting on us to merge your pull request. We'll review all pull requests, and make suggestions and changes if necessary.

# Additional Resources

* [General GitHub documentation](http://help.github.com/)
* [GitHub pull request documentation](http://help.github.com/send-pull-requests/)
* [AxisThemes Docs](http://docs.axisthemes.com/)
* [AxisThemes Support](http://support.axisthemes.com)
