# SSO-PoC: auth-keycloak-modulen krever GMP eller BCMath, som mangler i
# basisimaget. Lite overlegg så utvidelsen overlever container-gjenoppbygging.
FROM humhub/humhub:1.18.4
RUN install-php-extensions bcmath
