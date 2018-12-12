import { api_path } from './../../environments/global';
import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class CollectionsService {
  api_url = `${api_path}collections`;
  collection_change_url = `${api_path}collection-change`;

  constructor(private http: HttpClient) { }

  getCollections(): Observable<any[]> {
    return this.http.get<any[]>(this.api_url);
  }

  sync_collection(collectionID: number):Promise<any>{
    const promise:Promise<any> = new Promise<any>((resolve, reject) => {
      this.http.put(`${this.api_url}/${collectionID}`,{})
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

  collection_change(collectionID:number, params:any) : Promise<any>{
    const promise  = new Promise<any>((resolve, reject) => {
      this.http.put(`${this.collection_change_url}/${collectionID}`, params)
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
  getSingle(collectionID: number): Promise<any> {


    const promise  = new Promise<any>((resolve, reject) => {
      this.http.get(`${this.api_url}/${collectionID}`)
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
