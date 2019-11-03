# CapitalOne
Capital One's Engineering Summit Programming Challenge 2019

*Build a web app to find trivia questions by category*

### Technologies used

PHP, Javascript, HTML, CSS, JQuery, MySQL

### Features

- Users are able to log in; passwords are stored securely via hashing/salting
- Logged in users can save questions to a favorites collection
- Users and non-users can simulate a Jeopardy board
- Use of jService's search API to match categories with a user's queries in real time (non-mobile only)
  - This was done through web scraping in pure Javascriipt
- Inspired by flat UI

### Notice

Searching, loading the game board simulation, and the category refiner may experience some latency due to calling for large amounts of data from the jService API.
