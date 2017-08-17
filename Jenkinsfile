pipeline {
    agent none
    stages {
        stage('Unit tests PHP 7.1') {
            agent {
                label 'php71'
            }
            steps {
                sh 'composer install'
                sh './vendor/bin/phpunit --testsuite unitTests'
            }
        }
        stage('Unit tests PHP 7.0') {
            agent {
                label 'php70'
            }
            steps {
                sh 'composer install'
                sh './vendor/bin/phpunit --testsuite unitTests'
            }
        }
    }
}
