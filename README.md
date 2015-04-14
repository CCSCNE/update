# update

Updates one or more clones using `git pull` whenever update.php is ran. Suitable for use with GitHub webhooks (and possibly others).

## License

* Copyright 2015, Stoney Jackson <dr.stoney@gmail.com>
* License: GPL v3

## Requirements

* PHP 5+
* git 2+
* GitHub repository (or any service that provides webhooks)

## get started


    $ git clone url-of-source one
    $ git clone url-of-source two
    $ cd two
    $ git checkout devel

Clone the repository to a location under your websites document root.

    $ cd /location/under/document/root
    $ git clone https://github.com/CCSCNE/update.git
    
Edit update.php and add paths to $clones to the repositories you want updated.

    $ vim update/update.php

In GitHub, add URL to update.php as a webhook on the source repository.

Now whenever you push to the source repository, the webhook will notify update.php, which will run `git pull` on each
listed clone.
