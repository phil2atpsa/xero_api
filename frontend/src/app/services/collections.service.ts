import { api_path } from './../../environments/global';
import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class CollectionsService {
  api_url = `${api_path}collections`;

  constructor(private http: HttpClient) { }

  getCollections(): Observable<any[]> {
    return this.http.get<any[]>(this.api_url);
  }
}
