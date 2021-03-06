# BuyMeADrink-Server

Student project

## Short API Documentation 


Default response for all routes if something goes wrong on server
```sh
{"Success":false,"Error":"Something went wrong,try again later."} status:500
```

/api/user

	Method GET
	
##### Possible response: 
```sh    
{"Success":true,"Data":users in JSONArray} status:200
{"Success":false,"Error":"No users in data base."}	status:200
```

##### Description: Return all users, if exists.
 
/api/user/id

	Method GET
	
##### Possible response: 
```sh    
{"Success":true,"Data":user in JSON} status:200
{"Success":false,"Error":"User doesn't exist."}	status:200
{"Success":false,"Error":"User id is not valid!"} status:200
```

##### Description:Return user with specific id. 

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
{"Success":true,"Data":{"friends_location":[{"lat":43.3217417,"lng":21.8976607,"_id":"57672b82e39c63d81e00002a"},{"lat":43.3217417,"lng":21.8976607,"_id":"576720bee39c63d81e000029"}],"friends_in_nearby":["57672b82e39c63d81e00002a","576720bee39c63d81e000029"],"questions_in_nearby":[]}} status:200
{"Success":false,"Error":"Wrong request check request body"}	status:200
```	
##### Description:Update own location on server,server wiil return locations for all friends_ids and add .

/api/user/friends/id

	Method GET
##### Possible response: 

```sh
{"Success":true,"Data":{[JSONdataUser,JSONdataUser,...]} status:200
{"Success":false,"Error":"User doesn't exist."}	status:200
{"Success":false,"Error":"User id is not valid!"} status:200
```
##### Description:Return user friends.

/api/question

	Method POST
	Request body -> JSONdata question
	
##### Possible response: 

```sh
{"Success":true,"Data":question in JSON} status:201
{"Success":false,"Error":"Wrong ownerID!"} status 200
{"Success":false,"Error":"Check request body."} status:200
```
##### Description:Adding new question in mongo database.

/api/question

	Method GET

##### Possible response:

```sh	
{"Success":true,"Data":questions in JSONArray} status:200
{"Success":false,"Error":"No questions in data base."}	status:200
```		
##### Description:Return all questions, if exists.

/api/question/id

	Method GET
	
##### Possible response:
```sh
{"Success":true,"Data":question in JSONObject} status:200
{"Success":false,"Error":"Question doesn't exist."}	status:200
{"Success":false,"Error":"Question id is not valid!"} status:200
```		
##### Description:Return question with specific id. 

/api/question/questionId/userId/answerNum

	Method DELETE
	
##### Possible response:
```sh
{"Success":true,"Data":"Question deleted."}	status:200
{"Success":false,"Error":"You can't answer on your own question!"}	status:200
{"Success":false,"Error":"Sorry but that answer is not correct, try again after one day."}	status:200
{"Success":false,"Error":"You are already try to answer on this question, try again after one day."}	status:200
{"Success":false,"Error":"Question does't exist. Maybe someone answer on this question in meanwhile."} status:200
{"Success":false,"Error":"You can't answer on this question because you are too far."} status:200
```		
##### Description:Delete question with specific id. If conditions are met. 

/api/question/{query}/{category}/{range}/{lat}/{lng}
	Method GET

	if range or category is not set then just put "NOT_SET"
	
##### Possible response:
```sh
{"Success":true,"Data":Questions in JSONArray }
{"Success":false,"Error":"There is no question does not meet the requirements."}
```		
##### Description:Return questions if conditions are met. 
	
