import { Component, OnInit, ViewChild} from '@angular/core';
import {AccountsService} from "../../../services/accounts.service";
import {MatSort, MatTableDataSource,MatPaginator,MatDialog, MatDialogConfig} from "@angular/material";
import {Router} from "@angular/router";
import {AccountFormComponent}  from "./account-form/account-form.component";

@Component({
  selector: 'app-accounts',
  templateUrl: './accounts.component.html',
  styleUrls: ['./accounts.component.scss']
})
export class AccountsComponent implements OnInit {

  list: MatTableDataSource<any>;
  searchKey ='';
  matDialogConfig: MatDialogConfig = new MatDialogConfig();

  displayedColumns: string[] = [
    'Code',
    'Name',
    'Type',
    'Status',
    'Class',
    'BankAccountNumber'
    

  ];

  @ViewChild(MatSort) sort: MatSort;
  @ViewChild(MatPaginator) paginator: MatPaginator;
  
  constructor(private accountService: AccountsService, private router:Router, private dialog: MatDialog) {
        this.matDialogConfig.disableClose = true;
        this.matDialogConfig.autoFocus = true;
        this.matDialogConfig.width = '55%';
  }

  ngOnInit() {
       this.refresh();
  }
  
   refresh() {
    this.accountService.getList().subscribe(
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
  
   onAccountCreate(){
    this.dialog.open(AccountFormComponent, this.matDialogConfig)
      .afterClosed()
      .subscribe(res => {
        this.refresh();
      });

  }

}
