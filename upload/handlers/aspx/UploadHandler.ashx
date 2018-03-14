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
using System.Reflection;
using System.Diagnostics;
using System.Web.Hosting;
using ScandVault;

  public class MyHandler:IHttpHandler {

      private string CurrentSize
      {
          set
          {
              HttpContext.Current.Application[HttpContext.Current.Request.QueryString["sessionId"].ToString()] = value;
          }
      
      }
      
      public void ProcessRequest(HttpContext context) {

          if (context.Request.QueryString["sessionId"].ToString() != "")
          {
              int uploadStatus = 0;
              FileProcessor fp = new FileProcessor(@"c:\upload\");
              fp._currentFileName = context.Request.QueryString["fileName"];
                            
              HttpWorkerRequest workerRequest = (HttpWorkerRequest)context.GetType().GetProperty("WorkerRequest", BindingFlags.Instance | BindingFlags.NonPublic).GetValue(context, null);
              if (workerRequest.HasEntityBody())
              {
                  try
                  {
                      long contentLength = long.Parse((workerRequest.GetKnownRequestHeader(HttpWorkerRequest.HeaderContentLength)));
                      long defaultBuffer = 100000;
                      long CurrentBytesTransfered = 0;
                      long receivedcount = 0;
                      MemoryStream mem = new MemoryStream(context.Request.ContentLength);


                      byte[] preloadedBufferData = workerRequest.GetPreloadedEntityBody();
                      CurrentBytesTransfered += preloadedBufferData.Length;

                      fp.GetFieldSeperators(ref preloadedBufferData);
                      fp.ProcessBuffer(ref preloadedBufferData, true);

                      if (preloadedBufferData == null)
                      {
                          throw new Exception("GetPreloadedEntityBody() was null.  Try again");
                      }

                      if (!workerRequest.IsEntireEntityBodyIsPreloaded())
                      {
                          do
                          {

                              long tempBufferSize = (contentLength - CurrentBytesTransfered);
                              if (tempBufferSize < defaultBuffer)
                              {
                                  defaultBuffer = tempBufferSize;
                              }

                              // Create the new byte buffer with the default size.
                              byte[] bufferData = new byte[defaultBuffer];

                              // Ask the worker request for the buffer chunk.
                              receivedcount = workerRequest.ReadEntityBody(bufferData, bufferData.Length);

                              // Update the status object.
                              CurrentBytesTransfered += bufferData.Length;
                              fp.ProcessBuffer(ref bufferData, true);
                              //mem.Write(bufferData, 0, bufferData.Length);

                              // Add the upload status to the appliation object. 
                              CurrentSize = Convert.ToString((CurrentBytesTransfered * 100 / contentLength));


                          } while (receivedcount != 0);

                      }
                      fp.Dispose();

                  }
                  catch (Exception ex)
                  {
                      CurrentSize = "-1";
                  }
                  finally
                  {
                      fp.CloseStreams();
                      CurrentSize = "-1";
                  }
                  
              }
              
         }

         
      }
      
      public bool IsReusable { 
          get { 
              return false; 
          } 
      }
      
      private string getFileName(string patch)
      {
          string[] arr = patch.Split(new string[] { @"\" }, StringSplitOptions.None);
          return arr[arr.Length-1].ToString();
      }  
  }

