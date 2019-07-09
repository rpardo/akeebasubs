# Akeeba Subscriptions 7

A Joomla component to sell one-time and recurring subscriptions using Paddle. 

**This version of Akeeba Subscriptions is dramatically different to versions 5 and 6. It is no longer a generic subscriptions component, it is a very tight integration with Paddle with features specific to our use case. Most features we did not use ourselves have been dropped. Please do not use unless you understand the implications.**

## Internal project - No support

Akeeba Subscriptions is a project internal to Akeeba Ltd. We use it specifically and exclusively as own site's subscriptions system. We make it available free of charge as an example of how to build a complex component using FOF and because someone out there might find it interesting, useful or inspiring.

Unlike our consumer-oriented, mass-distributed software, Akeeba Subscriptions is NOT to be considered as a feature-stable software. We will remove, modify or add features at any time, without prior notice, as we see fit. If you use it you might end up with a broken site or with an old version we no longer develop. If this happens please don't ask us to reinstate functionality, revert changes or continue developing an obsolete version. We have no interest, no time and no reason to do so.

Moreover, kindly note that we will not accept any feature requests, feature patches or support requests. This software is built specifically for our site, not _your_ site ;)

## Downloads

We publish _infrequent_ builds available for download from [this repository's Releases section](https://github.com/akeeba/akeebasubs/releases). Please note that these are not released or maintained regularly, nor are they meant for mass distribution. They simply represent a point in time where we consider the component to have achieved a set of internal objectives; we then tag it and create a ZIP for us to have an easily accessible rollback in case the next development stint goes sideways.

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