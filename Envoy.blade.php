@servers(['web' => 'deployer@devobbi.com'])

@setup
    $repository = 'git@gitlab.com:obbi-dev/blog.git';
    $releases_dir = '/var/www/apis/releases';
    $app_dir = '/var/www/apis';
@endsetup

@story('deploy')
    pull_repository
@endstory

@task('pull_repository')
    echo 'Pulling repository'
    cd {{ $releases_dir }}
    git status
    git reset --hard
    git pull origin master
    composer install
    php artisan migrate
    php artisan passport:install
    php artisan storage:link
    {{-- php artisan db:seed --}}
    npm install
@endtask


