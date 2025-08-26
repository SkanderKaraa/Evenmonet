pipeline {
    agent any  // On utilise le conteneur Jenkins existant

    environment {
        SONARQUBE_SERVER = 'sonarqube'
        DOCKER_IMAGE = 'evenmonet-app'
        PHP_CONTAINER = 'evenmonet_php'  // Conteneur PHP défini dans docker-compose
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', url: 'https://github.com/SkanderKaraa/Evenmonet.git'
            }
        }

        stage('Install dependencies') {
            steps {
                // Exécuter Composer directement dans le conteneur PHP
                sh "docker exec ${PHP_CONTAINER} composer install --no-interaction --prefer-dist"
            }
        }

        stage('SonarQube Analysis') {
            steps {
                withSonarQubeEnv("${SONARQUBE_SERVER}") {
                    sh """
                        docker run --rm \
                        -e SONAR_HOST_URL=${env.SONAR_HOST_URL} \
                        -e SONAR_LOGIN=${env.SONAR_AUTH_TOKEN} \
                        -v "\${PWD}:/usr/src" \
                        sonarsource/sonar-scanner-cli \
                        -Dsonar.projectKey=evenmonet \
                        -Dsonar.sources=src \
                        -Dsonar.php.coverage.reportPaths=coverage.xml
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
