# BrickMMO - Media

![Dashboard Screenshot](https://github.com/Pruthviraj7/media-v1/assets/72735146/f4cfd0fd-b878-416c-aab4-d2a96194016c)

## Overview

BrickMMO-Media is a comprehensive web application designed to facilitate the management and sharing of images and videos related to BrickMMO. Users can upload, view, download, and manage media content through an intuitive dashboard interface.

## Database Schema

### `users` Table

| Column   | Type         | Description                      |
|----------|--------------|----------------------------------|
| id       | INT          | Primary key, auto-increment     |
| username | VARCHAR(255) | Username of the user             |
| email    | VARCHAR(255) | Email address of the user        |
| password | VARCHAR(255) | Password hash of the user        |

### `images` Table

| Column      | Type         | Description                |
|-------------|--------------|----------------------------|
| id          | INT          | Primary key, auto-increment|
| imageName   | VARCHAR(255) | Name of the media file     |
| mediaType   | ENUM('image', 'video') | Type of media (image or video) |
| media       | LONGBLOB     | Binary data of the media   |
| created     | DATETIME     | Timestamp of media upload  |
| tags        | VARCHAR(255) | Comma-separated tags       |

### `downloads` Table

| Column        | Type         | Description                         |
|---------------|--------------|-------------------------------------|
| id            | INT          | Primary key, auto-increment         |
| media_id      | INT          | Foreign key referencing `media.id`  |
| download_count| INT          | Number of times the media was downloaded |
| UNIQUE KEY    | unique_media| Ensures each media has one entry    |

## Features

### User Authentication

Users can securely log in and log out of the application to access personalized features and manage their uploaded media.

![Login Screenshot](https://github.com/Pruthviraj7/media-v1/assets/72735146/397a5a8a-4f6f-4b14-9a5f-cc357d2ec05a)

### Media Upload

The application supports the uploading of both images and videos. Users can upload multiple files simultaneously.

![Upload Screenshot](https://github.com/Pruthviraj7/media-v1/assets/72735146/2b705242-3d17-4276-b5a6-a7a58bbc170e)

### Gallery View

Uploaded media is displayed in a visually appealing gallery format, allowing users to browse through the content easily.

![Gallery Screenshot](https://github.com/Pruthviraj7/media-v1/assets/72735146/b63c6c30-816c-4a99-a1b5-9a73f55e8bbc)

### Download Tracking

The application tracks the number of times each media file is downloaded, providing insights into user engagement.

![Download Screenshot](https://github.com/Pruthviraj7/media-v1/assets/72735146/421a4d9c-e4c7-4775-9a9d-4fd820a35baa)

### Media Management

Users can edit and delete their uploaded media files directly from the dashboard, enabling easy content management.

![Edit Screenshot](https://github.com/Pruthviraj7/media-v1/assets/72735146/b0855d0f-19af-43ad-bbcc-5c50c4bdbbd5)
![Delete Screenshot](https://github.com/Pruthviraj7/media-v1/assets/72735146/a117fa3f-b78b-4a0e-ae56-924be4bea475)

### Search Functionality

The application includes a search bar section, allowing users to search for specific media files based on tags or keywords.

![Search Screenshot](https://github.com/Pruthviraj7/media-v1/assets/72735146/ea18d359-cbe8-4af5-b052-c4d2f7951fa9)

### Current Work

Currently, Focusing on implementing video upload functionality to enhance the diversity of media content available on the platform. Stay tuned for updates!


