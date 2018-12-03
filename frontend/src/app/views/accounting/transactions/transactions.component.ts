import { Component, OnInit, ViewChild} from '@angular/core';
import {TransactionService} from "../../../services/transaction.service";
import {MatSort, MatTableDataSource,MatPaginator,MatDialog, MatDialogConfig} from "@angular/material";
import {Router} from "@angular/router";


@Component({
  selector: 'app-transactions',
  templateUrl: './transactions.component.html',
  styleUrls: ['./transactions.component.scss']
})
export class TransactionsComponent implements OnInit {

  list: MatTableDataSource<any>;
  searchKey ='';
  matDialogConfig: MatDialogConfig = new MatDialogConfig();

  displayedColumns: string[] = [
    'Type',
    'Contact',
    'BankCode',
    'Date',
    'Reference',
    'Status',
    'Total'
    

  ];

  @ViewChild(MatSort) sort: MatSort;
  @ViewChild(MatPaginator) paginator: MatPaginator;
  
  constructor(private transactionService: TransactionService, private router:Router, private dialog: MatDialog) {
        this.matDialogConfig.disableClose = true;
        this.matDialogConfig.autoFocus = true;
        this.matDialogConfig.width = '55%';
  }

  ngOnInit() {
       this.refresh();
  }
  
   refresh() {
    this.transactionService.getList().subscribe(
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
