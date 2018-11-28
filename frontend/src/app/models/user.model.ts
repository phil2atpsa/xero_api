
import {Deserializable} from './deserializable.model';

export class UserModel implements Deserializable {

  public id: number;
  public user_id: number;
  public name: string;
  public surname: string;
  public email: string;
  public gender: string;
  public city: string;
  public suburb: string;
  public address: string;
  public province: string;
  public dob: string;
  public postcode: number;
  public lat: number;
  public lng: number;
  public business_category: string;
  public role: number;
  public username: string;
  public password: string;
  public active: number;
  public created_at: string;
  public updated_at: string;
  public permissions: string[];

  deserialize(input: any): this {
    Object.assign(this, input);
    return this;
  }

}
