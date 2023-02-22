import {
  Get,
  Controller,
  Render,
  Post,
  UseGuards,
  Param,
  UseFilters,
} from '@nestjs/common';
import { JwtAuthGuard } from 'src/auth/jwt/jwt-auth-guards';
import { UnauthorizedExceptionFilter } from 'src/filters/unauthorized-exception.filter';
import { SpotsService } from './spots.service';

@Controller('spots')
export class SpotsViewsController {
  constructor(private readonly spotsService: SpotsService) {}

  @UseGuards(JwtAuthGuard)
  @Get('list')
  @UseFilters(new UnauthorizedExceptionFilter())
  @Render('spots/list')
  async root() {
    const spots = await this.spotsService.getSpots();
    return { spots };
  }

  @UseGuards(JwtAuthGuard)
  @Get(':uuid')
  @UseFilters(new UnauthorizedExceptionFilter())
  @Render('spots/[uuid]')
  async getSpot(@Param('uuid') uuid: string) {
    const [spot] = await this.spotsService.getSpot(uuid);
    return { spot };
  }
}
