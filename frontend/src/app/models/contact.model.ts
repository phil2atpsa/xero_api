import {Deserializable} from './deserializable.model';

export class Contact implements Deserializable {

  public id: string;
  public status: string;
  public name: string;
  public email: string;
  public skype_user: string;
  public is_customer: boolean;
  public is_supplier: boolean;
  public city: string;
  public region: string;
  public postal_code: string;
  public due: number;
  public mobile_number: string;

  deserialize(input: any): this {
    Object.assign(this, input);
    return this;
  }

}
