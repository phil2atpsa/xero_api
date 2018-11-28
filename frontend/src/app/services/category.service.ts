import { Injectable } from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import {api_path} from '../../environments/global';


@Injectable({
  providedIn: 'root'
})
export class CategoryService {
  private categoryUrl: string = `${api_path}category/`;
  private headers = new HttpHeaders({'Access-Control-Allow-Origin': '*'} );
  constructor(private http: HttpClient) { }

  getParents(): Promise<any> {
    const promise = new Promise((resolve, reject) => {
      this.http.get(this.categoryUrl + 'parent', {headers: this.headers})
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
