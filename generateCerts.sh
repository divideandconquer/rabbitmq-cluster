#!/bin/sh

read -p "Enter an SSL Common Name [rabbit]: " CN
read -s -p "Enter an SSL Passphrase: " PP

#default CN to rabbit
commonName="rabbit"
if [[ -z "$CN" ]]; then
  CN=$commonName
fi

#clean up old certs
find ssl/* -name "*.pem" -type f -delete
find ssl/* -name "*.p12" -type f -delete
rm -f ssl/testca/index.txt*
rm -f ssl/testca/serial*
rm -f ssl/testca/cacert.cer
rm -rf ssl/testca/certs
rm -rf ssl/testca/private

cd ssl/testca
mkdir certs private
chmod 700 private
touch index.txt
echo 01 > serial

openssl req -x509 -config openssl.cnf -newkey rsa:2048 -days 365 -out cacert.pem -outform PEM -subj /CN=$CN/ -nodes
openssl x509 -in cacert.pem -out cacert.cer -outform DER

cd ../server
openssl genrsa -out key.pem 2048
openssl req -new -key key.pem -out req.pem -outform PEM -subj /CN=$CN/O=server/ -nodes

cd ../testca
openssl ca -config openssl.cnf -in ../server/req.pem -out ../server/cert.pem -notext -batch -extensions server_ca_extensions

cd ../server
openssl pkcs12 -export -out keycert.p12 -in cert.pem -inkey key.pem -passout pass:$PP
cat cert.pem key.pem > certkey.pem

cd ../client
openssl genrsa -out key.pem 2048
openssl req -new -key key.pem -out req.pem -outform PEM -subj /CN=$CN/O=client/ -nodes

cd ../testca
openssl ca -config openssl.cnf -in ../client/req.pem -out ../client/cert.pem -notext -batch -extensions client_ca_extensions

cd ../client
openssl pkcs12 -export -out keycert.p12 -in cert.pem -inkey key.pem -passout pass:$PP
cat cert.pem key.pem > certkey.pem