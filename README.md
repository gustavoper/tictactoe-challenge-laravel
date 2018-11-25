# tictactoe-challenge-laravel


A Tic-Tac Toe Challenge using Laravel, Docker and all that modern stack you know. Feel free to use it as a reference.


## Usage


Rename ```.env.example``` to ```.env```

Run ```./setup```...


Then ```./up```...

And... boom! Acess your app through browser on ```http://localhost:8080```


## Known Issues

Sometimes, you get a 419 error when running Api Tests. I'll check that soon.

In other times, you can get a "access denied" error when building your container on ```./setup```. 
This can be solved modifying folder "storage" privileges.