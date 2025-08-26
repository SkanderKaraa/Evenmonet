pipeline {
    agent any

    environment {
        SONARQUBE_SERVER = 'sonarqube' // Nom configuré dans Jenkins
        DOCKER_IMAGE = 'evenmonet-app'
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', url: 'https://ton-repo.git'
            }
        }

        stage('Install dependencies') {
            steps {
                sh 'composer install --no-interaction --prefer-dist'
            }
        }

        stage('Run tests') {
            steps {
                sh 'php bin/phpunit --coverage-clover=coverage.xml'
            }
            post {
                always {
                    junit '**/tests/report/*.xml'
                }
            }
        }

        stage('SonarQube Analysis') {
            steps {
                withSonarQubeEnv('sonarqube') {
                    sh """
                        sonar-scanner \
                        -Dsonar.projectKey=evenmonet \
                        -Dsonar.sources=src \
                        -Dsonar.php.coverage.reportPaths=coverage.xml \
                        -Dsonar.host.url=http://localhost:9000
                    """
                }
            }
        }

        stage('Build Docker Image') {
            steps {
                sh "docker build -t ${DOCKER_IMAGE}:latest ."
            }
        }

        stage('Deploy') {
            steps {
                sh "docker run -d -p 8082:80 --name evenmonet ${DOCKER_IMAGE}:latest"
            }
        }
    }

    post {
        always {
            echo 'Pipeline terminée'
        }
        success {
            echo '✅ Déploiement réussi'
        }
        failure {
            echo '❌ Erreur dans la pipeline'
        }
    }
}
