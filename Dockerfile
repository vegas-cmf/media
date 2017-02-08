FROM amsdard/phalcon:3.x
MAINTAINER Radoslaw Fafara <radek@amsterdamstandard.com>

ENV apt_update "apt-get update"
ENV apt_install "apt-get -y install --no-install-recommends"
ENV apt_clean "rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*"

# Install Imagick PHP module with required packages
RUN ${apt_update} && ${apt_install} libmagickwand-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && ${apt_clean}
