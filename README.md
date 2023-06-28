# Newtify

## Team

- Bruno Rosendo, up201906334@fe.up.pt
- João Mesquita, up201906682@fe.up.pt
- Jorge Costa, up201706518@fe.up.pt
- Rui Alves, up201905853@fe.up.pt

## Artefact Checklist

The artefacts checklist is available [here](https://docs.google.com/spreadsheets/d/1t7sIAx0rBl6YObh1_6ypMGdorJFQ6nPCqpsOSO0cHY4/edit?usp=sharing).

## Installation

Final version of the source code [here](https://git.fe.up.pt/lbaw/lbaw2122/lbaw2111/-/tags/Final)

Full Docker command to test the group's Docker Hub image using the DBM database:
```
docker run -it -p 8000:80 --name=lbaw2111 -e DB_DATABASE="lbaw2111" -e DB_SCHEMA="lbaw2111" -e DB_USERNAME="lbaw2111" -e DB_PASSWORD="iROcBrWt" git.fe.up.pt:5050/lbaw/lbaw2122/lbaw2111
```

## Usage
 
URL to the product: http://lbaw2111.lbaw.fe.up.pt
 
#### Administration Credentials

| Username | Password |
| -------- | -------- |
| lbawadmin@fe.up.pt    | lbawadmin123 |

#### User Credentials

| Type          | Username  | Password |
| ------------- | --------- | -------- |
| basic account | lbawuser@fe.up.pt    | lbawuser123 |
| suspended account   | lbawsuspended@fe.up.pt    | lbawsuspended123 |
