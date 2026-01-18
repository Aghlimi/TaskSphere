`# TaskSphere

A comprehensive task and project management system built with Laravel.

> **⚠️ Note: This project is currently in development and actively being worked on.**

## About

TaskSphere is a collaborative project management application that enables teams to organize projects, create features, manage tasks, and track assignments. The system includes real-time notifications, team invitation workflows, and comprehensive event tracking.

## Features

- **Project Management**: Create and manage projects with full lifecycle tracking
- **Feature Tracking**: Break down projects into manageable features
- **Task Management**: Organize work with detailed task assignments
- **Team Collaboration**: Invite members and manage team access
- **Assignment System**: Assign tasks to team members with acceptance/rejection workflow
- **Real-time Notifications**: Get notified about project updates, invitations, and assignments
- **Event-Driven Architecture**: Comprehensive event and listener system for all major actions
- **Error Logging**: Built-in error tracking and monitoring

## Tech Stack

- **Framework**: Laravel 11.x
- **PHP**: 8.2+
- **Authentication**: Laravel Sanctum
- **Frontend Integration**: Inertia.js
- **Development Tools**: Laravel Telescope
- **Testing**: PHPUnit

## Project Structure

```
app/
├── Events/              # Application events
├── Listeners/           # Event listeners
├── Models/              # Eloquent models (User, Project, Task, Feature, etc.)
├── Notifications/       # Notification classes
├── Policies/            # Authorization policies
├── Http/
│   ├── Controllers/     # Request handlers
│   └── Requests/        # Form validation requests
routes/
├── api.php             # API routes
├── web.php             # Web routes
└── [Entity folders]/    # Organized route files
```

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd TaskSphere
```

2. Install dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env` file

6. Run migrations:
```bash
php artisan migrate
```

7. (Optional) Seed the database:
```bash
php artisan db:seed
```

## Usage

Start the development server:
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Key Models

- **User**: Application users
- **Project**: Main project container
- **Feature**: Project features
- **Task**: Individual tasks within features
- **Member**: Team members associated with projects
- **Assign**: Task assignments to users
- **Invitation**: Team invitation system

## Events & Notifications

The application implements a robust event-driven system including:
- Project creation notifications
- Feature and task creation alerts
- Assignment acceptance/rejection workflows
- Team invitation system
- Member management notifications
- Error logging and tracking

## Testing

Run the test suite:
```bash
php artisan test
```

Or using PHPUnit directly:
```bash
./vendor/bin/phpunit
```

## Development Status

This project is currently under active development. Features and APIs may change without notice.
