FriggFlight 0.1
========

A modern REST API of the data provided by Avinor. Built using Symfony2.

Currently under heavy development so the structure is still subject to big changes. Though the base API structure is nearing completion.

**Here's what we got so far:**

- [x] Imports
    - [x] Flight
    - [x] Airline
    - [x] Airport
    - [x] FlightStatus
- [x] Relations
    - [x] Airline to Flight
    - [x] Airport to Flight
    - [x] Flight to FlightStatus
    - [x] Import service to LastUpdated
- [x] Rest API
    - [x] CRUD controller actions
    - [x] ViewResponseListener returning XML/JSON/HTML based on request
    - [x] Automatic routing
- [x] FormTypes

**To-Do list:**

- [ ] Rest API
	- [ ] QueryParameters
- [ ] Security
    - [ ] FOSUserBundle
    - [ ] FOSOAuthServerBundle
    - [ ] Signup, register, receive key
- [ ] Frontend
    - [ ] Dashboard
    - [ ] Statistics
    - [ ] Flight surveillance
    - [ ] User accounts
    - [ ] Documentation
- [ ] Unit tests