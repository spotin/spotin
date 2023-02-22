import { Get, Controller, Render, Post, UseGuards } from '@nestjs/common';
import { JwtAuthGuard } from 'src/auth/jwt/jwt-auth-guards';
import { SpotsService } from './spots.service';

@Controller('spots')
export class SpotsViewsController {
  constructor(private readonly spotsService: SpotsService) {}

  @UseGuards(JwtAuthGuard)
  @Get('list')
  @Render('spots/list')
  async root() {
    const spots = await this.spotsService.getSpots();
    return { spots };
  }
}
