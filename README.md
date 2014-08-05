FriggFlight 0.1 Alpha
========

A modern REST API of the data [provided by Avinor](http://www.avinor.no/avinor/trafikk/50_Flydata). Built with [Symfony 2.3](https://github.com/symfony/symfony).

**Progress:**

- [x] Stage- and production environments
- [x] Imports
    - [x] Flight w/options
    - [x] Airline
    - [x] Airport
    - [x] FlightStatus
- [x] Mapping
    - [x] Airline to Flight
    - [x] Airport to Flight
    - [x] Flight to FlightStatus
    - [x] Import service to LastUpdated
- [x] Rest API
    - [x] CRUD controller actions
    - [x] ViewResponseListener returning XML/JSON/HTML based on request
    - [x] Automatic routing
    - [x] getFlights() must accomodate flight_status_time
    - [x] QueryParameters
    	- [x] Airport
    	- [x] Airline
    	- [ ] Extend with more parameters
- [x] FormTypes
- [ ] Security
	- [x] Only admin can POST/PUT/DELETE 
    - [x] FOSUserBundle
    - [ ] FOSOAuthServerBundle
    - [ ] Signup, register, receive key
- [x] [A Demo site](http://www.flyapi.no/demo)
- [ ] Documentation
	- [x] NelmioApiDocBundle
	- [x] Write the most important about each action
	- [ ] Proof read and details
- [x] Deployment procedure using Git post-update-hooks
- [ ] Unit tests
