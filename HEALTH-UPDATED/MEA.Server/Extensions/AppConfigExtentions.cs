using MEA.Server.Entities;
using Microsoft.EntityFrameworkCore;

namespace MEA.Server.Extensions
{
    public static class AppConfigExtentions
    {
        public static WebApplication ConfigureCORS(this WebApplication app,
            IConfiguration config)
        {
            app.UseCors("AllowAngularApp");
            return app;
        }

        public static IServiceCollection AddAppConfig(
           this IServiceCollection services,
           IConfiguration config)
        {
            services.Configure<AppSettings>(config.GetSection("AppSettings"));
            
            // Add CORS services
            services.AddCors(options =>
            {
                options.AddPolicy("AllowAngularApp", policy =>
                {
                    policy.SetIsOriginAllowed(_ => true)
                    .AllowAnyMethod()
                    .AllowAnyHeader()
                    .AllowCredentials();
                });
            });
            
            return services;
        }
    }

}

