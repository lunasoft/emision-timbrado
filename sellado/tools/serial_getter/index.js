#!/usr/bin/env node

'use strict';

const program = require('commander');

var su = function(af, cb){
    var ser = ''
    for(var i=1;af.length;i= i+2){
          ser += af.charAt(i)
    }
   return cb(ser)

}
program
  .version('0.0.1')
  .option('-s, --serial','serial certificado')
    .action(function(af){
       
        var ser = ''
        for(var i=1;i<af.length;i= i+2){
          ser += af.charAt(i)
      }
      console.log('el certificado es:');
      console.log(ser)
    })
  .parse(process.argv);
 