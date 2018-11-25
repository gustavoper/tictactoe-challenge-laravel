# tictactoe-challenge-laravel


A Tic-Tac Toe Challenge using Laravel, Docker and all that modern stack you know. Feel free to use it as a reference.


## Usage


Rename ```.env.example``` to ```.env```

Run ```./setup```...


Then ```./up```...

And... boom! Acess your app through browser on ```http://localhost:8080```

## Tests

I've written a few unit test cases, they are on ```tests``` folder. You can easily run them via `phpunit`.

Please note that `TicTacToePocTest.php` is a Proof-of-Concept (POC) of the algorhithm. That's my own implementation and I've used pieces of this code into my solution, that's why I decided to keep this script on `tests` folder.


## Known Issues

Sometimes, you get a 419 error when running Api Tests. I'll check that soon.

In other times, you can get a "access denied" error when building your container on ```./setup```. 
This can be solved modifying folder "storage" privileges.