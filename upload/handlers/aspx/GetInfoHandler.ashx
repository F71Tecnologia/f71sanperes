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

          if (context.Request["sessionId"] != null)
          {
              context.Response.ContentType = "text/plain";
              if (context.Application[context.Request["sessionId"]] != null)
              {
                  int percent = Convert.ToInt32(context.Application[context.Request["sessionId"]].ToString());
                  context.Response.Write(percent);
                  if (percent == -1)
                  {
                      context.Application.Remove(context.Request["sessionId"].ToString());
                  }

              }

          }
          else
          {
              context.Response.ContentType = "text/plain";
              context.Response.Write(-1);
          }
     
      }
      
      public bool IsReusable { 
          get { 
              return false; 
          } 
      } 
  }

