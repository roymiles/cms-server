# Web development project

## Summary
Zeal is a web development project that allow users to create fully database driven websites without having to use any back end code. This means that there needs to be an extensive and open API for the user to query.

A javascript library will be written to query the API and synchronise the users website with their 'backend' logic securely.

At the moment, this project is in its very early stages and is a mere proof of concept. I may take this project in a different direction as I see fit.

The project is written with the [Symfony2](https://symfony.com/) framework and has made extensive use of their FOS bundles to speed up dev

- Note: all modules that are used on the site must be available externally via the API


## TODO

### General
- [ ] Generic table interface and template
- [ ] Sort table by columns
- [ ] Generic pagination widget
- [ ] Inline editable tables
- [ ] Authentication system for table edits (generic interface?)
- [ ] Remove github markdown css

### Documentation
- [x] Get JBBCode markdown working
- [ ] Breadcumb links
- [ ] Child documentation links in a widget
- [ ] **BBCode for inline API responses**

### Javascript framework
- [ ] Integrate a secure and tested OAUTH2 library
- [ ] Create a session management service
- [ ] Get the login
- [ ] Registration system working

### Small tasks
- [ ] Seperate tables for external and internal users
- [ ] Get basic API routes working with FOSRestBundle
- [ ] Get API documentation working with nelmio bundle

### Testing
- [ ] Try out the built in symfony2 logging system


- [ ] Try and ensure all controllers methods have minimal number of attached routes





