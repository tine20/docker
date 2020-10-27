# alt common names can be set in cert.cnf

openssl genrsa -out key.pem 2048
openssl req -new -out cert.csr -key key.pem -config cert.cnf
openssl x509 -in cert.csr --out cert.pem -req -signkey key.pem --days 2048 -extensions v3_req -extfile cert.cnf