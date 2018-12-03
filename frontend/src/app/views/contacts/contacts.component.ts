import {Component, OnInit, ViewChild} from '@angular/core';
import {ContactService} from "../../services/contact.service";
import {MatSort, MatTableDataSource,MatPaginator} from "@angular/material";
import {Router} from "@angular/router";
import {Contact} from "../../models/contact.model";



@Component({
  selector: 'app-contacts',
  templateUrl: './contacts.component.html',
  styleUrls: ['./contacts.component.scss']
})
export class ContactsComponent implements OnInit {

  list: MatTableDataSource<any>;
  searchKey ='';
  c_all = false;
  displayedColumns: string[] = [
    'check_actions',
    'name',
    'status',
    'email',
    'due',
    'actions',

  ];

  @ViewChild(MatSort) sort: MatSort;
  @ViewChild(MatPaginator) paginator: MatPaginator;

  constructor(private contactService: ContactService, private router: Router) { }

  ngOnInit() {
    this.refresh();
  }

  onContactCreate(){
    this.router.navigateByUrl("contacts/edit");

  }

  refresh() {
    this.contactService.getContactInfo().subscribe(
      list => {
        const array = list.map(
          item => {
            // console.log(item);
            return {...item};
          });
        this.list = new MatTableDataSource(array);
        this.list.sort = this.sort;
        this.list.paginator = this.paginator;
      }
    );
  }

  onSearchClear() {
    this.searchKey = '';
    this.applyFilter();
  }
  applyFilter() {
    this.list.filter = this.searchKey.trim().toLowerCase();

  }
  push(c: boolean, contact: Contact) {
   /* if ( c && this.stores_to_delete.indexOf(store) === -1) {
     // this.stores_to_delete.push(store);
    }
    if (!c && this.stores_to_delete.indexOf(store) !== -1) {
     // this.stores_to_delete.splice( this.stores_to_delete.indexOf(store), 1);
    }*/
  }
  onEdit(contact_id: string){
   //localStorage.setItem("contact_id", contact_id);
   //
    this.contactService.getSingleContact(contact_id).then(
        res => {
          localStorage.setItem('contact', JSON.stringify(res));
          this.router.navigateByUrl("contacts/edit");
        },
    );
  }
  onView(contact: Contact){
   this.contactService.getSingleContact(contact.id).then(
        res => {
          localStorage.setItem('contact', JSON.stringify(res));
          this.router.navigateByUrl("contacts/view");
        },
    );
  }

}
