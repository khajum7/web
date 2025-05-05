'use strict';
const AWS = require("aws-sdk");
let mysql = require('mysql');

const ses = new AWS.SES({
  region: 'us-east-1'
});
const nodemailer = require("nodemailer");

let pool = mysql.createPool({
        host: 'voxecommerce-cluster.cluster-c8jt3wis6ofs.us-west-1.rds.amazonaws.com',
        user: 'ecommMultiApi',
        password: '3RCdhMxHYqOM',
        database : 'ecommMultiApi', 
        port     : 3306,
        queueLimit : 0, // unlimited queueing
        connectionLimit : 0 // unlimited connections
    });
  
var RECEIVER = 'suman@colorstoweb.com';
var SENDER = 'sumanmahrjn@gmail.com';

exports.handler = async (event, context, callback) => {
    //prevent timeout from waiting event loop
    context.callbackWaitsForEmptyEventLoop = false;
    
    
    var params = {
    Destination: {
          ToAddresses: [
                  RECEIVER
              ]
      },
      Message: {
          Body: {
              Text: {
                  Data: 'My Name is Email',
                  Charset: 'UTF-8'
              }
          },
          Subject: {
              Data: 'Test : Email',
              Charset: 'UTF-8'
          }
      },
      Source: SENDER
  };
  
  
   ses.sendEmail(params, function (err, data) {
      callback(null, {err: err, data: data});
      if (err) {
          console.log(err);
          context.fail(err);
      } else {
          
          console.log(data);
          context.succeed(event);
      }
  });

}

let performDBQuery = async (sql, params) => {
    return new Promise((resolve, reject) => {``
        pool.getConnection((err, connection) => {
            if (err) { reject(err); }
            connection.query(sql, params, (err, results) => {
                connection.release();
                if (err) { reject(err); }
                resolve(results);
            });
        });
    });
};