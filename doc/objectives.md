# Project
## Objectives
The goal of the project is to provide a playground for more advanced ways of using EasyAdmin than the base presented in the [EasyAdmin demo][1].

For that I'll update the base of the project with advanced "functionalities" to increase a bit the "complexity" of the admin part.

All the updates/additions, I would like to make are listed here : [Updates](easyadmin_updates.md)

## Project structure

The project is based on the [EasyAdmin demo][1] which is itself based on the [Symfony Demo][2].

It's a blog, so we have the following entities:
- User : to author the blog posts
- Post : a post in the blog and must be created by someone logged into the system
- Tag : a Post can have multiple tags
- Comment : a Post can have multiple Comments and must be created by someone logged into the system

### Update to that structure
Some updates to the base structure are needed to enable some functionalities need in the admin part

#### 1. Addition of roles
The idea is to add the following roles:
- ROLE_ADMIN : an administrator that can do whatever they want in the system
- ROLE_AUTHOR : users that can create content
- ROLE_PUBLISHER : users that can publish/reject content (authors can't)
- ROLE_REVIEWER : users that can review and approve/reject comments
- ROLE_USER : users that can create comments and are not able to enter the Administration part

#### 2. Addition of Workflows
Addition of workflow on:

**Post**
- with the following statuses
  - Draft
  - In-review
  - Published
  - Cancelled
- and the following possible transitions
  - Draft to In-review : Author only
  - In-review to Published :  Publisher only
  - In-review to Cancelled :  Publisher only

**Comment**
- with the following statuses:
    - Created
    - Published
    - Rejected
- and the following possible transitions
    - Creation : All users can create a Comment
    - Created to Published :  Reviewer only
    - Created to Rejected :  Reviewer only

(all actions can be also done by the admins)

[1]: https://github.com/EasyCorp/easyadmin-demo
[2]: https://github.com/symfony/demo