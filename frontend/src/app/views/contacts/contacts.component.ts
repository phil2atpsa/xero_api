import {Component, OnInit, ViewChild} from '@angular/core';
import {ContactService} from '../../services/contact.service';
import {MatSort, MatTableDataSource, MatPaginator} from '@angular/material';
import {Router} from '@angular/router';



@Component({
  selector: 'app-contacts',
  templateUrl: './contacts.component.html',
  styleUrls: ['./contacts.component.scss']
})
export class ContactsComponent implements OnInit {

  list: MatTableDataSource<any>;
  searchKey = '';
  displayedColumns: string[] = [
    'name',
    'status',
    'email',
     'mobile_number',

  ];

  @ViewChild(MatSort) sort: MatSort;
  @ViewChild(MatPaginator) paginator: MatPaginator;

  constructor(private contactService: ContactService, private router:Router) { }

  ngOnInit() {
    this.refresh();
  }

  onContactCreate(){

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

}
