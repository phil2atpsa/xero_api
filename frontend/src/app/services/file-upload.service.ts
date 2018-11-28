import { Injectable } from '@angular/core';
import {HttpClient, HttpHeaders, HttpParams, HttpRequest} from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class FileUploadService {
  constructor(private http: HttpClient) { }
  public uploadFileToUrl(url: string, file: File, data_key: string,  data: string, input_name: string) {
     const fd = new FormData();
     if (file != null) {
       fd.append(input_name, file, file.name);
     }
    fd.append(data_key, data);
    const promise = new Promise((resolve, reject) => {
      this.http.post(url, fd)
        .toPromise()
        .then(
          res  => {
            resolve(res);
          },
          error => {
            reject(error);
          }
        );
    });
    return promise;
  }
}
