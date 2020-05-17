**Activate User Account**
----
Calling this service permits the activation of the specified user account.

* **URL**

  https://secureconnect.online/api/auth/activation

* **Method:**

  `GET`

*  **URL Params:**

   **Required:**
 
   `log` => test@example.com

   `key` => ageneratedtokenthatwillbegiventoyou123

* **Success Response:**

  * **Code:** 200 OK<br />
    **Content:** `{"success":"Activation Successful"}`

  * **Code:** 200 OK<br/>
    **Content:** `{"error":"Already Enabled"}`
 
* **Error Response:**

  * **Code:** 400 Bad Request<br />
    **Content:** `{"error":"Bad Request"}`

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "https://secureconnect.online/api/auth/activation",
      dataType: "json",
      type : "GET",
      data: 'log=test@example.com&key=c960c122316900482a6ded6c51181614',
      success : function(r) {
        console.log(r);
      }
    });
  ```