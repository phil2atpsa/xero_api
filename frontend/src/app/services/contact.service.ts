import { Injectable } from '@angular/core';
import {HttpClient, HttpHeaders} from "@angular/common/http";
import {api_path} from '../../environments/global';
import {Observable} from "rxjs";
import {Contact} from "../models/contact.model";
import {Contact_formModel} from "../models/contact_form.model";


@Injectable({
  providedIn: 'root'
})
export class ContactService {
  private info_url =`${api_path}contact/infos`;
  private contact_api =`${api_path}contacts`;


  constructor(private  http: HttpClient) {}

  public getContactInfo(): Observable<Contact[]> {
    return this.http.get<Contact[]>(this.info_url);
  }
  public getContacts(): Observable<any[]> {
    return this.http.get<any[]>(this.contact_api);
  }
  
  
  public saveContact(post:any, contactId: string | null){


    let promise = null;
    if(contactId != null){
      promise = new Promise<any>((resolve, reject) => {
        this.http.put(`${this.contact_api}/${contactId}`, post  )
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
    } else {
      promise = new Promise<any>((resolve, reject) => {
        this.http.post(this.contact_api, post)
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
    }
    return promise;
  }
  public getSingleContact(contact_id : string) : Promise<any> {


    const promise  = new Promise<any>((resolve, reject) => {
      this.http.get(`${this.contact_api}/${contact_id}`)
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
