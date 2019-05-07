# WMDE Fundraising App Runtime

This repository provides Dockerfiles for the different [WMDE Fundraising Appplication](https://github.com/wmde/FundraisingFrontend) environments.

* [`latest`](https://github.com/wmde/fundraising-frontend-docker/blob/master/latest/Dockerfile) - The base image, containing the PHP runtime and all necessary extensions, including the custom [konto_check](https://sourceforge.net/projects/kontocheck/) extension for validating German IBAN.
* [`dev`](https://github.com/wmde/fundraising-frontend-docker/blob/master/dev/Dockerfile) - Similar to the base image, but with [ssmtp](https://wiki.debian.org/sSMTP) for sending mails on port 1025 to a dummy hostname called `mailhog`.
* [`xdebug`](https://github.com/wmde/fundraising-frontend-docker/blob/master/xdebug/Dockerfile) - Similar to `dev`, but with [xdebug](https://xdebug.org/) installed. With this image you can debug the application and generate code coverage information during unit tests.
* [`composer`](https://github.com/wmde/fundraising-frontend-docker/blob/master/composer/Dockerfile) - A drop-in replacement for the `composer` command, compatible with all requirements in the `composer.json` file of the Fundraising Application.
* [`stab`](https://github.com/wmde/fundraising-frontend-docker/blob/master/stan/Dockerfile) - Static code analyzer PHPStan that runs inside the runtime environment of the base image.


## Regenerating the Dockerfiles
The Dockerfiles are automatically generated from templates which contain shared similar steps. If you want to change the contents of the dockerfiles, change the templates in [`generate_dockerfiles.php`](generate_dockerfiles.php) and run the script again.

## Building the images

To build a Dockerfile for local use or for testing, use the following command:

    docker build -t wikimediade/fundraising-frontend:dev dev

The images contain [metadata labels](https://docs.docker.com/develop/develop-images/dockerfile_best-practices/#label) with information about the build version, git commit id and build date. You need to provide this metadata with the `--build-arg` argument. Have a look at the [`build.sh`](build.sh) script as an example on how to build all images with the right tags.

To build all images and push them to the Docker Hub repository, run

    ./build.sh -p

If you want to version different builds, add a version tag to the environment names like this:

    ./build.sh -p -t 1.2.0

This will create build tagged with `latest-1.2.0`, `dev-1.2.0`, etc.
