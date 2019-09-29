# github-class-count

This project explores a given GitHub repository, goes through all class names from _src_ folder an returns a list of all words contained within class names and the total number that word appeared.

Clone the repository and then download all the vendors through **composer**:

**composer install**

Then launch the embedded web server:

**php bin/console server:run**

A form will appear with two fields:

- GitHub user name
- GitHub repository name

Once you click the call to action button a list of all the words will appear, ordered by total appearances (descent)

###Example

input: _myuser/myrepo_

Existing classes:
- src/Service.php
- src/MainController.php
- src/tests/TestService.php
- src/tests/TestController.php

Expected output:

- Test: 2
- Service: 2
- Controller: 2
- Main: 1

