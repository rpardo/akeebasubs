# Akeeba Subscriptions 5

**This version of Akeeba Subscriptions is written on FOF 3. It requires Joomla! 3.4 or later and PHP 5.4.0 or later.**

This branch contains the current, actively developed version of Akeeba Subscriptions based on FOF 3. You can study this code as a good example of a real world application utilising the full power of FOF 3. There are things in here you won't find in the FOF 3 documentation.

## Internal project - No support

Akeeba Subscriptions is a project internal to Akeeba Ltd. We use it as own site's subscriptions system. We make it available free of charge to everyone in hope that it will be useful. However, we will not accept any feature requests, feature patches or support requests. Emails (including through our business site's or personal sites' contact forms), GitHub Issues and Pull Requests containing any of these will be deleted / closed without reply. Thank you for your understanding.

## Downloads

We provide _infrequent_ builds available for download from [this repository's Releases section](https://github.com/akeeba/akeebasubs/releases). Please note that these are not released or maintained regularly. We urge developers to build their own packages using the instructions provided below.

## Build instructions

### Prerequisites

In order to build the installation packages of this component you will need to have the following tools:

* A command line environment. Using Bash under Linux / Mac OS X works best. On Windows you will need to run most tools through an elevated privileges (administrator) command prompt on an NTFS filesystem due to the use of symlinks. Press WIN-X and click on "Command Prompt (Admin)" to launch an elevated command prompt.
* A PHP CLI binary in your path
* Command line Git executables
* PEAR and Phing installed, with the Net_FTP and VersionControl_SVN PEAR packages installed
* (Optional) libxml and libsxlt command-line tools, only if you intend on building the documentation PDF files

You will also need the following path structure inside a folder on your system

* **akeebasubs** This repository. We will refer to this as the MAIN directory
* **buildfiles** [Akeeba Build Tools](https://github.com/akeeba/buildfiles)
* **fof** [Framework on Framework](https://github.com/akeeba/fof)
* **fef** [Akeeba Front-end Framework](https://github.com/akeeba/fef)
* **translations** [Akeeba Translations](https://github.com/akeeba/translations)

You must use the exact folder names specified here.

## Building a dev release

Go inside `akeebasubs/build` and run `phing git -Dversion=0.0.1.a1` to create a development release. The installable Joomla! ZIP package file is output in the `akeebasubs/release` directory.

If you want to build a release with development versions of FOF and FEF you will need to do some preparatory work. **This is NOT RECOMMENDED for most people**. Development builds of FOF and FEF may affect how other Akeeba and / or third party software work on your site. As a result you MUST NOT distribute these packages or use them on a production site. The steps you need to do are (from the main directory where you checked out all the other repositories):
```bash
pushd fof/build
phing git
popd
pushd fef/build
phing compile
popd
pushd akeebasubs/build
phing git 
popd 
```
This will create a dev release ZIP package in `akeebasubs/release`.
	
## Collaboration

If you have found a bug you can submit your patch by doing a Pull Request on GitHub. Please do respect the rules set forth earlier in this document. Thank you! 
