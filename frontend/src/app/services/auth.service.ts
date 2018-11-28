import {Inject, Injectable} from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import {UserModel} from '../models/user.model';
import 'rxjs-compat/add/operator/map';
import {api_path} from '../../environments/global';




@Injectable()
export class AuthService {

  private user_key = 'user';
  private authUrl: string = api_path + 'user/auth';
  private registerUrl: string = api_path + 'user/register';
  private headers = new HttpHeaders({'Content-Type': 'application/json'} );
  private currentUser: UserModel;
  constructor(private http: HttpClient) {}

  public getCurrentUser(): UserModel | null {
    if (localStorage.getItem(this.user_key) != null) {
      const current_user = JSON.parse(localStorage.getItem(this.user_key));
      return new UserModel().deserialize(current_user);
    }
    return null;
  }

  public login(username: string, password: string): Promise<UserModel> {
    const promise = new Promise<UserModel>((resolve, reject) => {
        this.http.post(this.authUrl, { 'username': `${username}`, 'password': `${password}` }, {headers: this.headers})
          .toPromise()
          .then(
            res => {
              this.currentUser = new UserModel().deserialize(res);
              localStorage.setItem(this.user_key, JSON.stringify(this.currentUser ));
              resolve();
            },
            error => {
              reject(error);
            }
          );
    });
    return promise;
  }

  public register(username: string , password: string, email: string): Promise<UserModel> {
    const promise = new Promise<UserModel>((resolve, reject) => {
      this.http.post(this.registerUrl, { 'username': `${username}`, 'password': `${password}`, 'email': `${email}`},
        {headers: this.headers})
        .toPromise()
        .then(
          res => {
            this.currentUser = new UserModel().deserialize(res);
            localStorage.setItem(this.user_key, JSON.stringify(this.currentUser ));
            resolve();
          },
          error => {
            reject(error);
          }
        );
    });
    return promise;
  }

  public logout() {
    this.currentUser = null;
    localStorage.removeItem(this.user_key);
  }


}
