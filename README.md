FriggFlight 0.1 Alpha
========

A modern REST API of the data [provided by Avinor](http://www.avinor.no/avinor/trafikk/50_Flydata). Built with [Symfony 2.3](https://github.com/symfony/symfony).

Currently under heavy, sporadic development so everything is subject to change at any time. However, the REST API structure is nearing completion.

No documentation is available yet. Meanwhile you can probably figure out how it works by looking at the routing table.

	php app/console router:debug

**CHANGELOG:**

- [x] [Stage](http://dev.flyapi.no)- and [production](http://www.flyapi.no) environments
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
    - [ ] QueryParameters
    	- [x] Airport
    	- [ ] Airline
- [x] FormTypes
- [ ] Security
    - [x] FOSUserBundle
    - [ ] FOSOAuthServerBundle
    - [ ] Signup, register, receive key
- [ ] Frontend
    - [ ] Dashboard
    - [ ] Statistics
    - [ ] Flight surveillance
    - [ ] User accounts
    - [ ] Documentation
- [ ] Deployment procedure using Git post-update-hooks
- [ ] Server monitoring w/alerts
- [ ] Unit tests
