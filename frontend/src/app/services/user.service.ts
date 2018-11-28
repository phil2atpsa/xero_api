import { Injectable } from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';

import {UserModel} from '../models/user.model';
import {api_path} from '../../environments/global';

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private search_by_username_url: string = api_path + 'user/username_search';
  private update_password: string = api_path + 'user/change_password/';
  private headers: Headers;
  constructor(private http: HttpClient) {
    this.headers = new Headers();
  }

  public searchByUsername(username: string): Promise<any> {
    const promise = new Promise<any>((resolve, reject) => {
      this.http.post(this.search_by_username_url, {'username': username})
        .toPromise()
        .then(
          res => {
            resolve(res);
          },
          error => {
            reject(error);
          }
        );
    });
    return promise;
  }

  public updatePassword(user_id: string, password: string): Promise<any> {
    const promise = new Promise<any>((resolve, reject) => {
      this.http.patch( this.update_password + user_id + '/' + password, {} )
        .toPromise()
        .then(
          res => {
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
