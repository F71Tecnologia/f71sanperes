<%@ WebHandler Language="C#" Class="MyHandler" %>
using System;
using System.Data;
using System.Configuration;
using System.Web;
using System.Web.Security;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Web.UI.HtmlControls;
using System.IO;
using System.Net;
using System.Collections;
using System.Text;
using System.Threading;


  public class MyHandler:IHttpHandler { 
      public void ProcessRequest (HttpContext context) {


              Guid id = Guid.NewGuid();
              context.Response.ContentType = "text/plain";
              context.Application.Add(id.ToString(), 0);
              context.Response.Write(id);
              

     
      }
      
      public bool IsReusable { 
          get { 
              return false; 
          } 
      } 
  }

