# EAP: Architecture Specification and Prototype

Newtify aims to bring an innovative news system to the market, whose content is fully maintained by its users. This Web Application allows users to contribute to the community, by writing news or reading them, and providing their feedback.

## A7: High-level architecture. Privileges. Web resources specification

The goal of this artefact is to make an overview of all web resources that will be implemented, which are organized in modules based on their functionality. Additionally, the necessary permissions, URL's and HTTP methods are defined for high priority user stories and resources implemented in the vertical prototype, using OpenAPI.

### 1. Overview 

| Module Name                        | Module Description                                                                                                                                                                                                                |
|------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| M01 : Authentication               | Web resources associated with user authentication, including the following system features: login, logout, sign-up and password recovery.                                                                  |
| M02 : Profile and User Information | Web resources associated with profile management and user data, including the following system features: view and edit profile, see followed users, report, follow and unfollow other users, account deletion, fetch user articles and areas of expertise.                     |
| M03 : Articles                     | Web resources associated with articles, including the following system features: view home page feed, article page, search and filter, provide feedback, create, edit, delete.                                                    |
| M04 : Comments                     | Web resources associated with comments, including the following system features: create, edit and delete comments, fetch comments of an article, provide feedback.                                                                |
| M05 : Tags                         | Web resources associated with tags, including the following system features: view and filter tags, propose new tags, change tag state, view and edit users' favorite tags, remove tags.                                           |
| M06 : Messages and Notifications   | Web resources associated with messages and notifications, including the following system features: view and create messages, search messages by user, view notifications and mark both as read.                                   |
| M07 : User Administration          | Web resources associated with administration of users, including the following system features: suspend/unsuspend users, view suspended users and suspension history, manage tags, see and close user reports.   |
| M08 : Static Pages                 | Web resources associated with static pages, including the following system features: about us, FAQs and guidelines.                                                                                                               |


### 2. Permissions

| Abbreviation | Name           | Description                                              |
|--------------|----------------|----------------------------------------------------------|
| PUB          | Public         | Unauthenticated User                         |
| SUR          | Suspended User | Suspended User with Read-only permissions            |
| USR          | User           | Authenticated User                                      |
| OWN          | Owner          | User who's owner of the content (article or comment) or account |
| ADM          | Administrator  | System Administrator                                    |

### 3. OpenAPI Specification

OpenAPI specification in YAML format to describe the web application's web resources.

Link to the `.yaml` file in the group's repository: https://git.fe.up.pt/lbaw/lbaw2122/lbaw2111/-/blob/main/a7_openapi.yaml

Link to the Swagger generated documentation: https://app.swaggerhub.com/apis-docs/BrunoRosendo/lbaw-newtify_api/1.0

```yaml
openapi: 3.0.0

info:
  version: "1.0"
  title: "LBAW Newtify API"
  description: "Web Resources Specification (A7) for Newtify"

servers:
  - url: lbaw2111.lbaw.fe.up.pt
    description: Production server

externalDocs:
  description: Find more info here.
  url: https://git.fe.up.pt/lbaw/lbaw2122/lbaw2111/-/wikis/home

tags:
  - name: "M01: Authentication"
  - name: "M02: Profile and User Information"
  - name: "M03: Articles"
  - name: "M04: Comments"
  - name: "M05: Tags"
  - name: "M06: Messages and Notifications"
  - name: "M07: User Administration"
  - name: "M08: Static Pages"

paths:
  # M01: Authentication
  /login:
    get:
      operationId: "R101"
      summary: "R101: Login Form"
      description: "Provide form for authentication. Access: PUB"
      tags:
        - "M01: Authentication"

      responses:
        "200":
          description: "Ok. Show Login form (UI02)"

    post:
      operationId: R102
      summary: "R102: Login action"
      description: "Processes the login form submission. Access: PUB"
      tags:
        - "M01: Authentication"

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                email:
                  type: string
                  format: email
                password:
                  type: string
                  format: password
              required:
                - email
                - password

      responses:
        "302":
          description: "Redirect after processing the login credentials."
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: "Successful login. Redirect to homepage."
                  value: "/"
                302Failure:
                  description: "Failed login. Redirect to login form."
                  value: "/login"

  /logout:
    get:
      operationId: R103
      summary: "R103: Logout action"
      description: "Logout the current authenticated user. Access: USR"
      tags:
        - "M01: Authentication"

      responses:
        "302":
          description: "Redirect after processing logout."
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: "Successful logout. Redirect to homepage."
                  value: "/"

  /signup:
    get:
      operationId: "R104"
      summary: "R104: Sign-up Form"
      description: "Provide form for new users. Access: PUB"
      tags:
        - "M01: Authentication"

      responses:
        "200":
          description: "Ok. Show sign-up form (UI03)"

    post:
      operationId: "R105"
      summary: "R105: Sign-up action"
      description: "Processes the new user sign-up form submission. Access: PUB"
      tags:
        - "M01: Authentication"

      requestBody:
        required: true
        content:
          # since we want to upload binary files (avatar)
          multipart/form-data:
            schema:
              type: object
              properties:
                name:
                  type: string
                email:
                  type: string
                password:
                  type: string
                  format: password
                birthDate:
                  type: string
                  format: date
                avatar:
                  type: string
                  format: binary
                country:
                  type: string
              required:
                - name
                - email
                - birthDate
                - country
                - password

      responses:
        "302":
          description: "Redirect after processing the new user sign-up form."
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: "Successful registration. Redirect to user profile."
                  value: "/users/{id}"
                302Failure:
                  description: "Failed registration. Redirect to sign-up form."
                  value: "/signup"

  # M02: Profile and User Information

  /user/{id}:
    get:
      operationId: R201
      summary: "R201: View user profile"
      description: "Show the individual user profile. Access: PUB"
      tags:
        - "M02: Profile and User Information"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "Ok. Show user profile (UI20)"
        "404":
          description: "User not found"

    put:
      operationId: R202
      summary: "R202: Edit user profile action"
      description: "Processes the user edit profile form submission. Access: OWN"
      tags:
        - "M02: Profile and User Information"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      requestBody:
        required: true
        content:
          # since we want to upload binary files (avatar)
          multipart/form-data:
            schema:
              type: object
              properties:
                name:
                  type: string
                email:
                  type: string
                password:
                  type: string
                  format: password
                avatar:
                  type: string
                  format: binary
                country:
                  type: string
                description:
                  type: string
                city:
                  type: string

      responses:
        "302":
          description: "Redirect after processing the user updated information."
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: "Successful profile edition. Redirect to user profile."
                  value: "/user/{id}"
                302Failure:
                  description: "Failed profile edition. Redirect to edit profile form."
                  value: "/user/{id}/edit"

  # User deletion is hidden behind /api to prevent accidents
  /api/user/{id}:
    delete:
      operationId: R203
      summary: "R203: Delete user account action"
      description: "Processes a request to delete a user account. Access: OWN"
      tags:
        - "M02: Profile and User Information"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      responses:
        "302":
          description: "Redirect after deleting user account."
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: "Successful user account deletion. Redirect to homepage."
                  value: "/"
                302Failure:
                  description: "Failed to delete user account. Redirect to user profile."
                  value: "/user/{id}"

  /user/{id}/edit:
    get:
      operationId: R204
      summary: "R204: User profile edition form"
      description: "Provide form for user profile edition. Access: OWN"
      tags:
        - "M02: Profile and User Information"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "Ok. Show user profile edition form (UI21)"
        "401":
          description: "Unauthorized. Must be authenticated"
        "403":
          description: "Forbidden. Must be owner of the profile"
        "404":
          description: "User not found"

  /user/{id}/report:
    post:
      operationId: R205
      summary: "R205: Report user action"
      description: "Processes a report made on a user. Access: USR"
      tags:
        - "M02: Profile and User Information"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                reason:
                  type: string
              required:
                - reason

      responses:
        "200":
          description: "Successful user report"
        "400":
          description: "Failed to report user. Bad request"
        "401":
          description: "Unauthorized. Must be authenticated"
        "404":
          description: "User not found"

  /api/user/{id}/suspension:
    get:
      operationId: R206
      summary: "R206: Suspension information"
      description: "Provides information about the user suspension. Access: SUR OWN, ADM"
      tags:
        - "M02: Profile and User Information"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "Success."
          content:
            application/json:
              schema:
                type: object
                properties:
                  reason:
                    type: string
                  end_date:
                    type: string
                    format: date
                example:
                  reason: "The user was disrespectful in many occasions."
                  end_date: "04-12-2023"
        "401":
          description: "Unauthorized. Must be authenticated"
        "403":
          description: "Forbidden. Must be owner of the account or administrator"
        "404":
          description: "User not found"
        "409":
          description: "User not suspended"

  /user/{id}/followed:
    get:
      operationId: R207
      summary: "R207: Followed users"
      description: "Provides a page with the list of users that a given user follows. Access: OWN"
      tags:
        - "M02: Profile and User Information"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "Success. Show list of followed users (UI23)"
        "401":
          description: "Unauthorized. Must be authenticated"
        "403":
          description: "Forbidden. Must be owner of the profile"
        "404":
          description: "User not found"

  /api/user/{id}/articles:
    get:
      operationId: R208
      summary: "R208: User articles"
      description: "Provides a page component with a list of articles written by a given user, in chronological order. Access: PUB"
      tags:
        - "M02: Profile and User Information"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
        - in: query
          name: offset
          schema:
            type: integer
            minimum: 0
            default: 0
          required: false
        - in: query
          name: limit
          schema:
            type: integer
            minimum: 1
          required: false # returns all articles if not specified

      responses:
        "200":
          description: "Success. Show a list of articles written by the user (UI22)"
        "404":
          description: "User not found"

  /user/{id}/follow:
    post:
      operationId: R209
      summary: "R209: Follow user action"
      description: "Processes a request to follow another user. Access: USR"
      tags:
        - "M02: Profile and User Information"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "Success"
        "401":
          description: "Unauthorized. Must be authenticated"
        "404":
          description: "User not found"

  /user/{id}/unfollow:
    delete:
      operationId: R210
      summary: "R210: Unfollow user action"
      description: "Processes a request to unfollow another user. Access: USR"
      tags:
        - "M02: Profile and User Information"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "Success"
        "401":
          description: "Unauthorized. Must be authenticated"
        "404":
          description: "User not found"
        "409":
          description: "Conflict. User not followed"

  /api/search/users:
    get:
      operationId: R211
      summary: "R211 : User search"
      description: "Provides a page component with a list of users, according to the parameters. Access: PUB"
      tags:
        - "M02: Profile and User Information"

      parameters:
        - in: query
          name: value
          description: "string used for full-text search"
          schema:
            type: string
          required: true
        - in: query
          name: offset
          description: "value used to fetch results starting at a certain value"
          schema:
            type: integer
            minimum: 0
            default: 0
          required: false
        - in: query
          name: limit
          description: "value used to limit the number of results"
          schema:
            type: integer
            minimum: 1
          required: false # returns all users if not specified

      responses:
        "200":
          description: "OK. Show a list of users that fit the criteria (part of UI19)"

  # M03 : Articles
  /:
    get:
      operationId: R301
      summary: "R301: Get Home Page"
      description: "Fetch Home Page of the application that depends on the user. Access: PUB"
      tags:
        - "M03: Articles"

      responses:
        "200":
          description: "OK. Show home page feed (UI01)"

  /search:
    get:
      operationId: R302
      summary: "R302 : Search Page"
      description: "Show Search Page given a search type and value. Access: PUB"
      tags:
        - "M02: Profile and User Information"
        - "M03: Articles"

      parameters:
        - in: query
          name: type
          description: "Search type"
          schema:
            type: string
            enum:
              - "article"
              - "user"
          required: true
        - in: query
          name: query
          description: "string used for full-text search"
          schema:
            type: string
          required: false

      responses:
        "200":
          description: "OK. Show Search Page (UI19)"

  /api/search/articles:
    get:
      operationId: R303
      summary: "R303 : Returns articles according to given parameters"
      description: "Returns search results according to user parameters. Access: PUB"
      tags:
        - "M03: Articles"

      parameters:
        - in: query
          name: query
          description: "string used for full-text search"
          schema:
            type: string
          required: false
        - in: query
          name: offset
          description: "value used to fetch results starting at a certain value"
          schema:
            type: integer
            minimum: 0
            default: 0
          required: false
        - in: query
          name: limit
          description: "value used to limit the number of results"
          schema:
            type: integer
            minimum: 1
          required: false # returns all articles if not specified

      responses:
        "200":
          description: "OK. Show a list of articles that fit the criteria"

  /api/article/filter:
    get:
      operationId: R304
      summary: "R304 : Return articles according to certain filters"
      description: "Show filtered articles by certain criteria. Access: PUB"
      tags:
        - "M03: Articles"

      parameters:
        - in: query
          name: type
          description: "feed type"
          schema:
            type: string
            default: "trending"
            enum:
              - "trending"
              - "recommended"
              - "recent"
          required: false
        - in: query
          name: offset
          description: "value used to fetch results starting at a certain value"
          schema:
            type: integer
            minimum: 0
            default: 0
          required: false
        - in: query
          name: limit
          description: "value used to limit the number of results"
          schema:
            type: integer
            minimum: 1
          required: false # returns all articles if not specified

      requestBody:
        required: false
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                tags:
                  type: array
                minDate:
                  type: string
                  format: date
                maxDate:
                  type: string
                  format: date

      responses:
        "200":
          description: "OK. Show filter results (Part of UI01)"
        "400":
          description: "Failed to filter articles. Bad Request"

  /article:
    get:
      operationId: R305
      summary: "R305 : Create article form"
      description: "Provide new article creation form. Access: USR"
      tags:
        - "M03: Articles"

      responses:
        '200':
          description: 'Ok. Show Create Article Form (UI17)'
        '302':
          description: 'Redirect after trying to get Create Article Form'
          headers:
            Location:
              schema:
                type: string  
              examples:
                302Failure:
                  description: 'You are not logged in. Failed article creation. Redirect to login Page.'
                  value: '/login' 

    post:
      operationId: R306
      summary: "R306 : Create article action"
      description: "Processes the new article creation form. Access: USR"
      tags:
        - "M03: Articles"

      requestBody:
        required: true
        content:
          # since we want to upload binary files (thumbnail)
          multipart/form-data:
            schema:
              type: object
              properties:
                title:
                  type: string
                thumbnail:
                  type: string
                  format: binary
                body:
                  type: string
                tag_ids:
                  type: array
                  minLength: 1
                  maxLength: 3
              required:
                - title
                - body
                - tag_ids

      responses:
        "302":
          description: "Redirect after processing the create article form."
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: "Successful article creation. Redirect to Article Page."
                  value: "/article/{id}"
                302Failure:
                  description: "Failed article creation. Redirect to Create Article form."
                  value: "/article"

  /article/{id}:
    get:
      operationId: R307
      summary: "R307 : View Article Page"
      description: "View an Article Page. Access: PUB"
      tags:
        - "M03: Articles"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "OK. Show Article Page (UI13)"
        "404":
          description: "Article not found"

    delete:
      operationId: R308
      summary: "R308 : Delete article action"
      description: "Processes a request to delete an article. Access: OWN, ADM"
      tags:
        - "M03: Articles"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      responses:
        302:
          description: "Redirect after deleting article."
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: "Successful article deletion. Redirect to homepage."
                  value: "/"
                302Failure:
                  description: "Failed to delete article. Redirect to article page"
                  value: "/article/{id}"

  /article/{id}/edit:
    get:
      operationId: R309
      summary: "R309 : Article edition form"
      description: "Provide a form for article edition. Access: OWN"
      tags:
        - "M03: Articles"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "OK. Show article edition form (UI14)"
        "401":
          description: "Unauthorized. Must be authenticated."
        "403":
          description: "Unauthorized. Must be the owner of the article"
        "404":
          description: "Article not found"

    put:
      operationId: R310
      summary: "R310 : Edit article action"
      description: "Processes the user edit profile form submission. Access: OWN"
      tags:
        - "M03: Articles"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      requestBody:
        required: true
        content:
          # since we want to upload binary files (thumbnail)
          multipart/form-data:
            schema:
              type: object
              properties:
                title:
                  type: string
                thumbnail:
                  type: string
                  format: binary
                body:
                  type: string

      responses:
        "302":
          description: "Redirect after processing the article updated information"
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: "Successful article edition. Redirect to the article page."
                  value: "/article/{id}"
                302Failure:
                  description: "Failed article edition. Redirect to the edit article page."
                  value: "/article/{id}/edit"

  # M04 : Comments
  /comment:
    post:
      operationId: R401
      summary: "R401 : Create comment action"
      description: "Processes the new comment creation form. Access: USR"
      tags:
        - "M04: Comments"

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                body:
                  type: string
                article_id:
                  type: integer
                parent_comment_id:
                  type: integer
              required:
                - body
                - article_id

      responses:
        "200":
          description: "Successful comment creation. Return created comment"
        "400":
          description: "Failed to create comment. Bad request"
        "401":
          description: "Unauthorized. Must be authenticated."
        "404":
          description: "[Article | Parent Comment] id not found."

  /comment/{id}:
    put: # Do we need a form endpoint to edit the comment? I believe it could be in the HTML already returned by the article. However, I've added one to create a comment and maybe it's the same logic
      operationId: R402
      summary: "R402 : Edit comment action"
      description: "Processes a comment edition. Access: OWN"
      tags:
        - "M04: Comments"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                body:
                  type: string

      responses:
        "200":
          description: "Successful comment update. Return updated comment"
        "400":
          description: "Failed to update comment. Bad request"
        "401":
          description: "Unauthorized. Must be authenticated."
        "403":
          description: "Forbidden. Must be the owner of the comment."
        "404":
          description: "Comment id not found."

    delete:
      operationId: R403
      summary: "R403 : Delete comment action"
      description: "Processes a request to delete a comment. Access: OWN, ADM"
      tags:
        - "M04: Comments"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "Successful comment deletion. Return deleted comment"
        "401":
          description: "Unauthorized. Must be authenticated."
        "403":
          description: "Forbidden. Must be the owner of the comment."
        "404":
          description: "Comment id not found."

  /api/article/{id}/comments:
    get:
      operationId: R404
      summary: "R404 : Comments of a given article"
      description: "Returns comments of a given article according to user parameters. Access: PUB"
      tags:
        - "M04: Comments"
      parameters:
        - in: path
          name: id
          description: "article id"
          schema:
            type: integer
          required: true
        - in: query
          name: offset
          description: "value used to fetch results starting at a certain value"
          schema:
            type: integer
            minimum: 0
            default: 0
          required: false
        - in: query
          name: limit
          description: "value used to limit the number of results"
          schema:
            type: integer
            minimum: 1
          required: false # returns all articles if not specified

      responses:
        "200":
          description: "OK. Return a list of comments that fit the criteria"
        "404":
          description: "Article id not found."

  # M05: Tags

  /tags/{tag_id}/accept:
    put:
      operationId: "R501"
      summary: "R501: Accept a Tag"
      description: "Change the state of a tag to accepted. Access: ADM"
      tags:
        - "M05: Tags"

      parameters:
        - in: path
          name: tag_id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "Ok. Tag is now accepted"
        "401":
          description: "Unauthorized. Not logged in"
        "403":
          description: "Forbidden. No permissions"
        "404":
          description: "Not found"

  /tags/{tag_id}/reject:
    put:
      operationId: "R502"
      summary: "R502: Reject a Tag"
      description: "Change the state of a tag to rejected. Access: ADM"
      tags:
        - "M05: Tags"

      parameters:
        - in: path
          name: tag_id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "Ok. Tag was rejected successfully"
        "401":
          description: "Unauthorized. Not logged in"
        "403":
          description: "Forbidden. No permissions"
        "404":
          description: "Not found"

  /favorite_tags:
    get:
      operationId: R503
      summary: "R503: View User Favourite Tags"
      description: "Show user favourite tags. Access: USR"
      tags:
        - "M05: Tags"

      responses:
        "200":
          description: "Ok. Show favourite tags (Part of UI20)"
        "302":
          description: "Redirect if user is not logged in."
          headers:
            Location:
              schema:
                type: string
              examples:
                302Failure:
                  description: "User is not logged in. Redirect to login form."
                  value: "/login"

  /tags/{tag_id}/add_favorite:
    put:
      operationId: R504
      summary: "R504: Add a Tag to Favourites"
      description: "Processes the addition of favourite tag. Access: USR"
      tags:
        - "M05: Tags"

      parameters:
        - in: path
          name: tag_id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "Ok. Tag is now in favourites"
        "401":
          description: "Unauthorized. Not logged in"
        "404":
          description: "Not found"

  /tags/{tag_id}/remove_favorite:
    put:
      operationId: R505
      summary: "R505: Remove a Tag from Favourites"
      description: "Processes the removal of favourite tag. Access: USR"
      tags:
        - "M05: Tags"

      parameters:
        - in: path
          name: tag_id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "Ok. Tag is now removed from favourites"
        "401":
          description: "Unauthorized. Not logged in"
        "404":
          description: "Not found"
        "409":
          description: "Conflict. Tag is not a favourite"

  /api/tags/{tag_state}:
    get:
      operationId: R506
      summary: "R506: Filter Tags"
      description: "Show list of tags by state. Access: ADM"
      tags:
        - "M05: Tags"

      parameters:
        - in: query
          name: state
          description: (ACCEPTED, REJECTED, PENDING)
          schema:
            type: string
          required: true
      responses:
        "200":
          description: "Ok. Show tag list (Part of UI09)"
        "401":
          description: "Unauthorized. Not logged in"
        "403":
          description: "Forbidden. No permissions"
        "404":
          description: "Not found"

  /tags/{tag_id}:
    delete:
      operationId: R507
      summary: "R507: Delete tag"
      description: "Processes the delete tag request. Access: ADM"
      tags:
        - "M05: Tags"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "Ok. Tag was deleted successfully"
        "401":
          description: "Unauthorized. Not logged in"
        "403":
          description: "Forbidden. No permissions"
        "404":
          description: "Not found"

  /tags/new:
    post:
      operationId: R508'
      summary: "R508: Propose New Tag"
      description: "Processes the new tag form submission. Access: USR"
      tags:
        - "M05: Tags"
      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                tag_name:
                  type: string
              required:
                - tag_name
      responses:
        "200":
          description: "Ok. Tag was successfully proposed"
        "400":
          description: "Bad Request. Failed to propose a new tag"
        "401":
          description: "Unauthorized. Not logged in"

  # M06: Messages and Notifications

  /messages:
    get:
      operationId: R601
      summary: "R601: View Messages Inbox"
      description: "Show the Message Inbox of an user. Access: USR"
      tags:
        - "M06: Messages and Notifications"

      responses:
        "200":
          description: "Ok. Show Message Inbox (UI25)"
        "401":
          description: "Unauthorized. Not logged in"

  /messages/{user_id}:
    get:
      operationId: R602
      summary: "R602: User Message Thread"
      description: "Show messages thread between users. Access: USR"
      tags:
        - "M06: Messages and Notifications"

      parameters:
        - in: path
          name: user_id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "Ok. Show Message Thread (UI26)"
        "401":
          description: "Unauthorized. Not logged in"
        "404":
          description: "Not found"

    post:
      operationId: R603
      summary: "R603: Create Message"
      description: "Create a message to send to a user. Access: USR"
      tags:
        - "M06: Messages and Notifications"

      parameters:
        - in: path
          name: user_id
          schema:
            type: integer
          required: true

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                body:
                  type: string

      responses:
        "200":
          description: "Ok. Message sent."
        "401":
          description: "Unauthorized. Not logged in"
        "404":
          description: "Not found"

    put:
      operationId: R604
      summary: "R604: Mark Messages as Read"
      description: "Mark Messages with a user as Read. Access: USR"
      tags:
        - "M06: Messages and Notifications"

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: Success'
        "401":
          description: "Unauthorized. Not logged in"
        "404":
          description: "Not found"

  /notifications:
    get:
      operationId: R605
      summary: "R605: View Notifications"
      description: "Show user notifications. Access: USR"
      tags:
        - "M06: Messages and Notifications"

      responses:
        "200":
          description: "Ok. Show Notifications"
        "401":
          description: "Unauthorized. Not logged in"

    put:
      operationId: R606
      summary: "R606: Mark Notifications as Read"
      description: "Mark notification as read. Access: USR"
      tags:
        - "M06: Messages and Notifications"

      responses:
        "200":
          description: "Ok. Show Notifications"
        "401":
          description: "Unauthorized. Not logged in"

  # M07: User Administration

  /admin:
    get:
      operationId: "R701"
      summary: "R701: Administration Panel"
      description: "Administration Central Page. Access: ADM"
      tags:
        - "M07: User Administration"

      responses:
        "200":
          description: "Ok. Access to administration central page (UI05)"
        "400":
          description: "Bad request."
        "401":
          description: "Unauthorized. Not logged in"
        "403":
          description: "Forbidden. No permissions"
        "404":
          description: "Not found"

  /admin/suspensions:
    get:
      operationId: "R702"
      summary: "R702: View suspended users and suspension history"
      description: "Page with information about suspensions. Access: ADM"
      tags:
        - "M07: User Administration"

      responses:
        "200":
          description: "Ok. Show suspended users (UI07)"
        "400":
          description: "Bad request."
        "401":
          description: "Unauthorized. Not logged in"
        "403":
          description: "Forbidden. No permissions"
        "404":
          description: "Not found"

  /user/{user_id}/suspend:
    post:
      operationId: "R703"
      summary: "R703: Suspend User"
      description: "Processes a suspension made on a user. Access: ADM"
      tags:
        - "M07: User Administration"

      parameters:
        - in: path
          name: user_id
          schema:
            type: integer
          required: true

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                reason:
                  type: string
                end_time:
                  type: string
                  format: date
              required:
                - reason
                - end_time

      responses:
        "200":
          description: "Successfully add suspension to user"
        "400":
          description: "Bad request."
        "401":
          description: "Unauthorized. Not logged in"
        "403":
          description: "Forbidden. No permissions"
        "404":
          description: "Not found"

  /user/{user_id}/unsuspend:
    put:
      operationId: "R704"
      summary: "R704: Unsuspend User"
      description: "Processes the unsuspension of an user. Access: ADM"
      tags:
        - "M07: User Administration"

      parameters:
        - in: path
          name: user_id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "Successfully removed suspension from a user"
        "401":
          description: "Unauthorized. Not logged in"
        "403":
          description: "Forbidden. No permissions"
        "404":
          description: "Not found"

  /admin/reports:
    get:
      operationId: "R705"
      summary: "R705: View all the reports"
      description: "Page with information about all the reports. Access: ADM"
      tags:
        - "M07: User Administration"

      responses:
        "200":
          description: "Ok. Show all the reports (UI06)"
        "401":
          description: "Unauthorized. Not logged in"
        "403":
          description: "Forbidden. No permissions"
        "404":
          description: "Not found"

  /admin/reports/{report_id}/close:
    put:
      operationId: "R706"
      summary: "R706: Close a report"
      description: "Processes a report and closes it"
      tags:
        - "M07: User Administration"

      parameters:
        - in: path
          name: report_id
          schema:
            type: integer
          required: true

      responses:
        "200":
          description: "Ok. Report closed successfully."

  /admin/tags:
    get:
      operationId: "R707"
      summary: "R707: Manage Tags"
      description: "Page to manage the tags of the system. Access: ADM"
      tags:
        - "M07: User Administration"

      responses:
        "200":
          description: "Ok. Provide Manage Tags page (UI09)"
        "401":
          description: "Unauthorized. Not logged in"
        "403":
          description: "Forbidden. No permissions"
        "404":
          description: "Not found"

  # M08: Static Pages

  /about:
    get:
      operationId: "R801"
      summary: "R801: About Page"
      description: "Page with information about application and its developers. Access: PUB"
      tags:
        - "M08: Static Pages"

      responses:
        "200":
          description: "Ok. Show About Page (UI11)"

  /faq:
    get:
      operationId: "R802"
      summary: "R802: FAQ Page"
      description: "Page with most frequently asked questions. Access: PUB"
      tags:
        - "M08: Static Pages"

      responses:
        "200":
          description: "Ok. Show FAQ Page (UI10)"

  /guidelines:
    get:
      operationId: "R803"
      summary: "R803: Guidelines"
      description: "Page with guidelines of the system. Access: PUB"
      tags:
        - "M08: Static Pages"

      responses:
        "200":
          description: "Ok. Show Guidelines Page (UI12)"

```

---


## A8: Vertical prototype

The goal of the vertical prototype is to provide a first contact with the product being developed. This shows the process and technologies used throughout the system (e.g. database, user interface).

In the artefact, it's given a description of the implemented user stories and web resources, according to the prototype.

### 1. Implemented Features

#### 1.1. Implemented User Stories

| User Story reference | Name                   | Priority                   | Description                   |
| -------------------- | ---------------------- | -------------------------- | ----------------------------- |
| US001      | See Home feed                  | high     | As a *User*, I want to see a feed of news in the Home Page, so that I can view the currently trending articles                                               |
| US002      | Read articles                  | high     | As a *User*, I want to open an article's page, so that I can read all the details about it                                                                   |
| US003      | Read comments                  | high     | As a *User*, I want to read comments in an article, so that I can view other people's opinions and thoughts                                                  |
| US004      | Search                         | high     | As a *User*, I want to search articles and profiles, so that I can easily find what I desire                                           |
| US006      | View profiles                  | high     | As a *User*, I want to view other users' profile, so that I can know more about them and see their articles                                                  |
| US007      | See likes/dislikes             | medium     | As a *User*, I want to see how many likes and dislikes a given article or comment has, so that I can quickly understand how it was received by the community |
| US101      | Sign-in               | high     | As a *Visitor*, I want to sign in the website, so that I can use all the features that are only available to authenticated users          |
| US102      | Sign-up               | high     | As a *Visitor*, I want to register in the website, so that I can own a personal account and become an authenticated user                  |
| US201      | Logout                  | high     | As an <em>Authenticated User</em>, I want to be able, so that I can log out of my account                                                                        |
| US202      | View Personal Profile   | high     | As an <em>Authenticated User</em>, I want to be able to visit my own profile, so that I check what others can see about me (e.g username, posts, description...) |
| US203      | Edit Personal Profile   | high     | As an <em>Authenticated User</em>, I want to be able to edit my own profile, so that I can update information (e.g changing password, username, avatar...)       |
| US205      | Post Article            | high     | As an <em>Authenticated User</em>, I want to be able to post an article with specific tags, so that other users can see it                                       |
| US206      | Edit Article            | high     | As an <em>Authenticated User</em>, I want to be able to edit an already posted article, so that I update information about it                                    |
| US207      | Delete Article          | high     | As an <em>Authenticated User</em>, I want to be able to delete a previous post, so that an article I wrote is no longer visible to anyone                        |
| US212      | Follow User             | high     | As an <em>Authenticated User</em>, I want to be able to follow an Authenticated User, so that I can see his posts on my customized feed                          |
| US213      | Unfollow User           | high     | As an <em>Authenticated User</em>, I want to be able to unfollow an Authenticated User, so that my customized feed is not influenced with it                     |
| US215      | See Favorite Tags      | high     | As an <em>Authenticated User</em>, I want to be able to see my favorite tags, so that I can decide if I want to change them |
| US216      | Change Favorite Tags   | high     | As an <em>Authenticated User</em>, I want to be able to choose, change or delete my favorite tags, so that my feed gets customized according to them            |
| US217      | Report Users  | high   | As an <em>Authenticated User</em>, I want to be able to report other users, so that I can help to maintain the website's guidelines     |
| US225      | Propose New Tags        | medium   | As an <em>Authenticated User</em>, I want to be able to propose new tags to administrators, so that I can help with the improvement of the application           |
| US301      | Delete articles           | high     | As an *Administrator*, I want to be able to delete articles, so that the system rules are maintained                                       |
| US306      | Accept/Reject news tags             | high     | As an *Administrator*, I want to be able to accept a new tag proposed by *Authenticated Users*, so that they can use it, or reject it                     |
| US307      | Profile identification | high     | As an *Administrator*, I want to have an Identification on my profile, so that other users can see that I'm an *Administrator*                     |
| US307      | Remove tag             | medium   | As an *Administrator*, I want to be able to remove an existing tag of the system, so that It cannot be used anymore                              |


...

#### 1.2. Implemented Web Resources  

> Module M01: Authentication  

| Web Resource Reference | URL                            |
| ---------------------- | ------------------------------ |
| R101: Login form | GET /login |
| R102: Login action | POST /login |
| R103: Logout action | GET /logout |
| R104: Signup form | GET /signup |
| R105: Signup action | POST /signup |


> Module M02: Profile and User Information  

| Web Resource Reference | URL                            |
| ---------------------- | ------------------------------ |
| R201: View user profile | GET /user/{id} |
| R202: Edit user profile action | PUT /user/{id} |
| R203: Delete user action | DELETE /api/user/{id} |
| R204: Edit user profile form | GET /user/{id}/edit |
| R205: Report user action | POST /user/{id}/report |
| R206: User suspension information | GET /api/user/{id}/suspension |
| R208: User articles | GET /api/user/{id}/articles |
| R209: Follow user action | POST /user/{id}/follow |
| R210: Unfollow user action | DELETE /user/{id}/unfollow |
| R211: Search users | GET /api/search/users |


> Module M03: Articles  

| Web Resource Reference | URL                            |
| ---------------------- | ------------------------------ |
| R301: Home page | GET / |
| R302: Search page | GET /search |
| R303: Search articles | GET /api/search/articles |
| R304: Filter articles | GET /api/article/filter |
| R305: Create article form | GET /article |
| R306: Create article action | POST /article |
| R307: View article page | GET /article/{id} |
| R308: Delete article action | DELETE /article/{id} |
| R309: Edit article form | GET /article/{id}/edit   |
| R310: Edit article action | PUT /article/{id}/edit |

> Module M05: Tags  

| Web Resource Reference | URL                            |
| ---------------------- | ------------------------------ |
| R501: Accept tag | PUT /tags/{tag_id}/accept |
| R502: Reject tag | PUT /tags/{tag_id}/reject |
| R503: Favorite tags | PUT /favorite_tags |
| R504: Add favorite tag | PUT /tags/{tag_id}/add_favorite |
| R505: Remove favorite tag | PUT /tags/{tag_id}/remove_favorite |
| R506: Filter tags | GET /api/tags/{tag_state} |
| R507: View article page | GET /article/{id} |
| R508: Delete tag | DELETE /tags/{tag_id} |
| R509: Propose tag | POST /tags/new   |


> Module M07: User Administration  

| Web Resource Reference | URL                            |
| ---------------------- | ------------------------------ |
| R701: Administration Panel | GET /admin |
| R703: Suspend user | POST /user/{user_id}/suspend |
| R704: Unsuspend user | PUT /user/{user_id}/unsuspend |
| R706: Close report | PUT /admin/reports/{report_id}/close |
| R707: Manage tags page | GET /admin/tags |


### 2. Prototype

The prototype is available at: lbaw2111.lbaw.fe.up.pt

Credentials:
- Regular User:
    - email: lbawuser@fe.up.pt
    - password: lbawuser123
- Administrator:
    - email: lbawadmin@fe.up.pt
    - password: lbawadmin123

The code is available at: https://git.fe.up.pt/lbaw/lbaw2122/lbaw2111/-/tree/main

---


## Revision history

Changes made to the first submission:
- Changed article comments endpoint
***
GROUP2111, 03/01/2021
 
* Bruno Rosendo, up201906334@fe.up.pt
* João Mesquita, up201906682@fe.up.pt (Editor)
* Jorge Costa, up201706518@fe.up.pt
* Rui Alves, up201905853@fe.up.pt