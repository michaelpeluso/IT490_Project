<p align="center">
    <h1 align="center">The Cartographer</h1>
</p>
<p align="center">
    <em>An AI-generaeted vaction planner.</em>
</p>
<p align="center">
	<img src="https://img.shields.io/github/last-commit/michaelpeluso/IT490_Project?style=flat&logo=git&logoColor=white&color=0080ff" alt="last-commit">
	<img src="https://img.shields.io/github/languages/top/michaelpeluso/IT490_Project?style=flat&color=0080ff" alt="repo-top-language">
	<img src="https://img.shields.io/github/languages/count/michaelpeluso/IT490_Project?style=flat&color=0080ff" alt="repo-language-count">
<p>
<p align="center">
	<img src="https://img.shields.io/badge/PHP-8674e3.svg?style=flat&logo=php&logoColor=white" alt="php">
	<img src="https://img.shields.io/badge/HTML5-E34F26.svg?style=flat&logo=HTML5&logoColor=white" alt="HTML5">
	<img src="https://img.shields.io/badge/RabbitMQ-e37a34.svg?style=flat&logo=rabbitmq&logoColor=white" alt="RabbitMQ">
	
  <!--
  <img src="https://img.shields.io/badge/React-61DAFB.svg?style=flat&logo=React&logoColor=black" alt="React">
	<br>
	<img src="https://img.shields.io/badge/MongoDB-47A248.svg?style=flat&logo=MongoDB&logoColor=white" alt="MongoDB">
	<img src="https://img.shields.io/badge/JSON-000000.svg?style=flat&logo=JSON&logoColor=white" alt="JSON">
	<img src="https://img.shields.io/badge/Express-000000.svg?style=flat&logo=Express&logoColor=white" alt="Express">
 -->
</p>



# Overall Goal

Because of the recent changes in AI technology, we think it would be a beneficial idea to incorporate new up-and-coming technologies such as ChatGPT into existing mapping software in order to improve road trips and general navigation. We aim to create a navigation software similar to Google Maps. The user can input text prompts that will output a tailored vacation pack with transportation services, activities, and lodging locations. \
 \
As an example, Miami Beach, FL, is the desired location, but it is important to see landmarks during the drive. ChatGPT is wil be used to provide feedback on different stops and destinations that are available along a certain route given the user’s preferences. 


## Defining Project Success

The project is successful if it significantly improves the user experience of the mapping software, making it easier and more efficient for users to plan road trips and navigate to their destination. 

The incorporation of ChatGPT should enhance the accuracy and reliability of the navigation software’s response to text prompts, which provides users with helpful and relevant information about their desired destination. 

Ultimately, the project’s success is determined by its ability to achieve the overall goal by improving road trip planning, enhancing navigation features, and leveraging AI technology effectively to provide valuable information to users.

***

# Key Features

This is an application that allows users to instantly generate full vacation itineraries with any prompt.


### Generated with Artificial Intelligence

This project will be relying on the help of Chat GPT in order to create a vacation given a user's prompt as well as tune it towards the user’s preferences.


### General Routing

At a minimum, we want to offer the user the ability to automatically generate a route from one location to another. This includes route creation, vehicle rentals, plane ticketing, and public transport information.


### Lodging

With almost all of these planned vacations, our users must find a place to stay for the duration of the trip. We will use plenty of popular lodging sites like Tripadvisor, Booking.com, and Airbnb in order to give users the best options.


### Restaurant and Eateries

During this vacation, users can explore nearby restaurants and other places to eat. We want these options to be integrated into our app. Users can even specify parameters like food preferences and price.


### Activities

Of course, vacations are planned around activities. Users will be free to use our app to location preference-related activities near their location. These may include anything from clubs and sports games to water parks and arcades.


### Landmarks / Pit Stops

It’s important to take breaks while on a long, multiple-hour long road trip so our app plans on using the Google Maps API to locate rest stops, gas stations, and other similar stops to give the user the break they need from driving.

***

# Key Deliverables / Milestones


## General Deliverables

* Functional Web Site / Server
* Secured Database
* Inter Server Communication through a message Queue
* Data Collection through Code (Cron / on demand)
* Firewalls (with reject rules)
* Authentication


## Personal Deliverables
* User Input Gathered and Analyzed
* Connect to Chat GPT
* Successfully Map Routes
* Frontend Web Pages
    * Login/Register
    * Landing
    * Plan
    * Itinerary/Results
    * My Trips
* Integrate other APIs
    * Lodging
    * Activities
    * Transportation
    * Restaurants

***

# Estimated Schedule


<table>
  <tr>
   <td><strong>Week</strong>
   </td>
   <td><strong>Phases</strong>
   </td>
   <td><strong>Common Deliverables</strong>
   </td>
   <td><strong>Personal Deliverables</strong>
   </td>
  </tr>
  <tr>
   <td>1
   </td>
   <td>Introduction and Project Overview
   </td>
   <td>
   </td>
   <td>
   </td>
  </tr>
  <tr>
   <td>2
   </td>
   <td>Virtual Machine and web server
   </td>
   <td>
   </td>
   <td>
   </td>
  </tr>
  <tr>
   <td>3
   </td>
   <td>Database and message queueing system
   </td>
   <td>
   </td>
   <td>
   </td>
  </tr>
  <tr>
   <td>4
   </td>
   <td>User Authentication Functional
   </td>
   <td>Authentication
   </td>
   <td>
   </td>
  </tr>
  <tr>
   <td>5
   </td>
   <td>Distributed Logger Functional
   </td>
   <td>Inter Server Communication through a message Queue
   </td>
   <td>Frontend Web Pages (Login)
   </td>
  </tr>
  <tr>
   <td>6
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>Frontend Web Pages (Plan)
   </td>
  </tr>
  <tr>
   <td>7
   </td>
   <td>
   </td>
   <td>Firewalls
   </td>
   <td>User Input Gathered and Analyzed
   </td>
  </tr>
  <tr>
   <td>9
   </td>
   <td>Overview of Final Project Framework
   </td>
   <td>
   </td>
   <td>Connect to Chat GPT
   </td>
  </tr>
  <tr>
   <td>10
   </td>
   <td>Deployment Systems in Depth
   </td>
   <td>Secured Database
   </td>
   <td>Frontend Web Pages (Plan)
   </td>
  </tr>
  <tr>
   <td>11
   </td>
   <td>Multiple VM Tiers
<p>
Setup Deployment Server
<p>
SystemD for all custom tasks
   </td>
   <td>
   </td>
   <td>Successfully Map Routes
<p>
Integrate other APIs (Lodging, Transportation)
   </td>
  </tr>
  <tr>
   <td>12
   </td>
   <td> Deployment System Functional
   </td>
   <td>Data Collection through Code 
   </td>
   <td>Integrate other APIs (Activites, Restaurants)
   </td>
  </tr>
  <tr>
   <td>13
   </td>
   <td>Database Replication
<p>
Responsive Website, Hashed Passwords
   </td>
   <td>
   </td>
   <td>Frontend Web Pages (My Trips)
<p>
Frontend Web Pages (Results)
   </td>
  </tr>
  <tr>
   <td>14
   </td>
   <td>Failover / Backup functional
<p>
Firewalls and SSL setup
   </td>
   <td>Functional Web Site / Server
   </td>
   <td>Frontend Web Pages (Landing)
   </td>
  </tr>
  <tr>
   <td>15
   </td>
   <td>Final Design Challenge
<p>
Final Project Presentation
<p>
Gather Change  Logs
   </td>
   <td>
   </td>
   <td>
   </td>
  </tr>
</table>

***

# Tools


## VirtualBox

We use this virtualization program to install an image of  Ubuntu version 22.04. We use this Linux distribution to host and execute our project files.


## RabbitMQ

A message-broker software controlling messages sent between client and server.


## Apache Web Server

Used to host the web pages created for this project.

***

# API/Datasets

* AI Chat:

* ChatGPT: [https://openai.com/blog/openai-api](https://openai.com/blog/openai-api)

* Restaurants/Food 

    * Yelp: [https://docs.developer.yelp.com/docs/fusion-intro](https://docs.developer.yelp.com/docs/fusion-intro) 
    * Google Places: [https://developers.google.com/places/web-service/intro](https://developers.google.com/places/web-service/intro) 

*  Route mapping

    * Google Maps: [https://developers.google.com/maps](https://developers.google.com/maps) 
    * Transit App: [https://transitapp.com/apis](https://transitapp.com/apis) 

* Transportation APIs

    * Driving
        * Uber: [https://developer.uber.com/](https://developer.uber.com/) 
        * Lyft: [https://developer.lyft.com/](https://developer.lyft.com/) 
    * Airlines
        * FlightAware: [https://flightaware.com/commercial/flightxml/documentation2.rvt](https://flightaware.com/commercial/flightxml/documentation2.rvt)
        * United Airlines Developer Portal: [https://airlabs.co/united-airlines-developer-api](https://airlabs.co/united-airlines-developer-api) 
        * British Airways Developer Portal: [https://developer.iairgroup.com/british_airways](https://developer.iairgroup.com/british_airways) 

* Room Booking

    * Airbnb: [https://www.airbnb.com/partner](https://www.airbnb.com/partner) 
    * Booking.com API: [https://developers.booking.com/](https://developers.booking.com/) 
    * TripAdvisor API: [https://developer-tripadvisor.com/](https://developer-tripadvisor.com/) 

* Weather

    * Weather API (OpenWeatherMap): [https://openweathermap.org/api](https://openweathermap.org/api) 

* Currency Exchange

    * Currency Exchange API (Open Exchange Rates): [https://openexchangerates.org/documentation](https://openexchangerates.org/documentation) 

* Other general travel planning API

    * Expedia API: [https://developer.expediapartnersolutions.com](https://developer.expediapartnersolutions.com/)
    * [Skyscanner: https://skyscanner.github.io/slate/](https://developer.expediapartnersolutions.com/)

***

# UI Workflow


## Login / Register

Users sign in or create a new account. They now have access to all features. 


## Landing Page

Here is where users get a taste of what our application is capable of. This also includes a brief how-to tutorial as well as sample trips.


## My Trips

All trips can be seen here with small descriptions and notes.


## Plan a Trip

Users are prompted to input their preferences for their trip including budget, location, duration, date, etc.


## Itinerary / Results

User preferences for a specific trip are collected, parsed, and analyzed by calls to APIs. This is the output.
