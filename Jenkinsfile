pipeline {
    agent any

    environment {
        SONARQUBE_SERVER = 'sonarqube'
        DOCKER_IMAGE = 'evenmonet-app'
        PHP_CONTAINER = 'evenmonet_php'
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', url: 'https://github.com/SkanderKaraa/Evenmonet.git'
            }
        }

        stage('Install dependencies') {
            steps {
                sh "docker exec ${PHP_CONTAINER} composer install --no-interaction --prefer-dist"
            }
        }

        stage('SonarQube Analysis') {
            steps {
                withSonarQubeEnv("${SONARQUBE_SERVER}") {
                    sh """
                        docker exec ${PHP_CONTAINER} sonar-scanner \
                        -Dsonar.projectKey=evenmonet \
                        -Dsonar.sources=src \
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
                sh """
                    docker stop evenmonet || true
                    docker rm evenmonet || true
                    docker run -d -p 8082:80 --name evenmonet ${DOCKER_IMAGE}:latest
                """
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
