# BuyMeADrink-Server

Student project

## Short Server Documentation about API


Default response for all routes if something goes wrong on server
```sh
{"Success":false,"Error":"Something went wrong,try again later."} status:500
```

/api/user

	Method GET
	
#####Possible response: 
```sh    
{"Success":true,"Data":users in JSONArray} status:200
{"Success":false,"Error":"No users in data base."}	status:200
```

##### Description: Return all users.
 
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
	Request body -> Possible request:
```sh
{UserInJSON}
{UserInJSON + "image_base64":"ds3432432rfsav3...."} inside UserInJSON Object
```
	
##### Possible response: 
```sh
{"Success":true,"Data":user in JSON} status:200
{"Success":false,"Error":"Check request body."} status:200
```		
##### Description: Update user in mongo database


/api/login

	Method POST
	Request body -> JSONdata {"email":"user email","password":"secret"}
	
##### Possible response: 
    
```sh
{"Success":true,"Data":user in JSON} status:200
{"Success":false,"Error":"Wrong email or password!"}	status:200
{"Success":false,"Error":"Wrong request check request body"}	status:200
```
##### Description: Log in user if is everything OK Note:Request body must be as above exemple.

/api/updateLocation

	Method POST
	Request body -> JSONdata {"user_id":"555","lat":32.3,"lng":43.2,range:55,"friends":["3213213213213123211","3213213213213123211"]}
##### Possible response: 

```sh
 {"Success":true,"Data":{"friends_location":[{"lat":43.3217417,"lng":21.8976607},{"lat":43.3217417,"lng":21.8976607}],"friends_in_nearby":["57672b82e39c63d81e00002a","576720bee39c63d81e000029"],"questions_in_nearby":[]}} status:200

{"Success":false,"Error":"Wrong request check request body"}	status:200

```	
##### Description:Update own location on server,server wiil return locations for all friends_ids and add .