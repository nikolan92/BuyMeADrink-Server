# BuyMeADrink-Server

Student project

## Short Server Documentation about API


Default response for all routes if something goes wrong on server
```sh
{"Success":false,"Error":"Something went wrong,try again later."} status:500
```

/api/user

	Method POST
	Request body -> JSONdata user
    
##### Possible response: 
    
```sh
{"Success":true,"Data":user in JSON} status:201 
{"Success":false,"Error":"This email already exist!\nTry with other email."}	status:200
```
##### Description: Add new user in mongo database

/api/user

	Method PUT
	Request body -> JSONdata user
	
##### Possible response: 
```sh
{"Success":true,"Data":user in JSON} status:201
{"Success":false,"Error":"Check request body."} status:200
```		
##### Description: Update user in mongo database

/api/user/id

	Method GET
	
#####Possible response: 
```sh    
{"Success":true,"Data":user in JSON} status:200
{"Success":false,"Error":"User doesn't exist."}	status:200
```

##### Description: Return user with specific id Note:If id is not set correctly then response will be: 
```sh
{"Success":false,"Error":"Something went wrong,try again later."} 
```


/api/login

	Method POST
	Request body -> JSONdata {"email":"email","password"}
	
##### Possible response: 
    
```sh
{"Success":true,"Data":user in JSON} status:200
{"Success":false,"Error":"Wrong email or password!"}	status:200
{"Success":false,"Error":"Wrong request check request body"}	status:200
```
##### Description: Log in user if is everything OK Note:Request body must be as above exemple.
>>>>>>> 49d95dfd4e92d285103ec9899b71a899519d7237
