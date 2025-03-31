
# phpMasterClass

This repo is a playground for general object oriented paradigms in the context of PHP OO and it's latest Versions.

Topics in this repo loosely follow the course ITSYS1_AE of B-WI138-U-FI_240617 from 03/2025.

# Tech Stack

Overview, for more details read [[Documentation/dev-setup.md]].

Apple Mac Silicon
Docker Desktop
DDEV
Jetbrains phpStorm incl. Plugins: DDEV, Composer, .ignore
DataGrip stand-alone and as Plugin (DB setup and checks)

# Setup

## setup files

``` bash
# clone
git clone https://github.com/bodo/phpMasterClass.git

cd phpMasterClass

# prepare DDEV runtime
# choose type "php", "mariadb", use defaults
ddev config

# install phpmyadmin (as a container)
ddev add-on ddev/ddev-phpmyadmin

# enable xdebug
ddev xdebug

# show the DDEV config and URLs anytime with
ddev describe
```
## setup IDE

Start phpStorm and create a project with "Files -> New Project from Existing Files..."

in the phpStorm Terminal you can anytime
`ddev start` and `ddev stop` to start the web/MariaDB servers

Edit the file `.ddev/config.yaml` and change the PHP Version to a higher version, like

`php_version: "8.4"`

Do a `ddev restart` to rebuild & restart the Docker containers for the change to take effect.



### Enable & configure debugging 

Enabling xdebug slows down execution of Scripts. You can quickly enable/disable xdebug:

``` Shell

# to check if enabled
ddev xdebug status

# to toggle on/off
ddev xdebug

# to turn off
ddev xdebug off

# to turn on
ddev xdebug on
```




# Background

As of 01/2025 I am on the fast-track preparation for the degree in computer science (IHK Fachinformatiker Anwendungsentwicklung) in fall/winter 2025.
For this I joined the school bbq.de and I attend classes as a student and tutor.


