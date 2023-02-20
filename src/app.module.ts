import { Module } from '@nestjs/common';
import { AppController } from './app.controller';
import { AppService } from './app.service';
import { SpotsModule } from './spots/spots.module';
import { PrismaModule } from './prisma/prisma.module';

@Module({
  imports: [SpotsModule, PrismaModule],
  controllers: [AppController],
  providers: [AppService],
})
export class AppModule {}
